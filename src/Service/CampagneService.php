<?php

namespace App\Service;

use App\DTO\CampagneDTO;
use App\DTO\CotisationItemDTO;
use App\DTO\MembreCampagneDTO;
use App\DTO\SeanceSummaryDTO;
use App\Entity\Amende;
use App\Entity\Campagne;
use App\Entity\Cotisation;
use App\Enum\StatutPresence;
use App\Repository\CampagneRepository;
use App\Repository\CotisationRepository;

class CampagneService
{
    public function __construct(
        private readonly CampagneRepository $campagneRepository,
        private readonly CotisationRepository $cotisationRepository
    ) {}

    public function getCampagneDetail(int $id): ?array
    {
        $campagne = $this->campagneRepository->find($id);
        if (!$campagne) {
            return null;
        }

        $groupement = $campagne->getGroupement();
        $type = str_contains(strtolower($campagne::class), 'tontine') ? 'Tontine' : 'Épargne Calebasse';
        $per = $campagne->getPeriodicite();

        $campagneDTO = new CampagneDTO(
            id: $campagne->getId() ?? 0,
            nom: $campagne->getNom() ?? '',
            type: $type,
            dateDebut: $campagne->getDateDebutAt()?->format('d/m/Y') ?? '',
            dateFin: $campagne->getDateFinAt()?->format('d/m/Y') ?? '',
            montant: number_format((float) $campagne->getMontant(), 0, ',', ' ') . ' FCFA',
            periodicite: $per?->value ?? 'hebdomadaire',
            periodiciteLabel: $per?->label() ?? 'Hebdomadaire',
            jourRencontre: $campagne->getJourRencontre() ?? 'DIMANCHE',
            montantFixe: number_format((float) ($campagne->getMontantFixe() ?? 0), 0, ',', ' ') . ' FCFA',
            montantCaissePresent: number_format((float) ($campagne->getMontantCaissePresent() ?? 0), 0, ',', ' ') . ' FCFA',
            montantCaisseAbsent: number_format((float) ($campagne->getMontantCaisseAbsent() ?? 0), 0, ',', ' ') . ' FCFA',
            amendeJournaliere: number_format((float) ($campagne->getAmendeJournaliere() ?? 0), 0, ',', ' ') . ' FCFA',
            groupementId: $groupement?->getId() ?? 0,
            groupementNom: $groupement?->getNom() ?? '',
            exercicePeriode: $campagne->getExercice() ? sprintf('Exercice %s', $campagne->getExercice()->getDateDebut()?->format('Y') ?? '') : null,
            nombreMembres: count($campagne->getMembreCampagnes())
        );

        // Membres inscrits (MembreCampagneDTO)
        $participantsDTO = [];
        foreach ($campagne->getMembreCampagnes() as $mc) {
            $membre = $mc->getMembre();
            $user = $membre?->getCompte();
            $nomComplet = $user?->getNomComplet() ?? 'Membre #' . $mc->getId();

            $totalEpargne = 0.0;
            $totalCaisse = 0.0;
            foreach ($mc->getCotisations() as $cotis) {
                $totalEpargne += (float) $cotis->getMontantEpargne();
                $totalCaisse += (float) $cotis->getMontantCaisse();
            }

            $nbAmendes = count($mc->getAmendes());
            $aAmendeImpayee = false;
            foreach ($mc->getAmendes() as $amende) {
                if (!$amende->isPayee()) {
                    $aAmendeImpayee = true;
                    break;
                }
            }

            $participantsDTO[] = new MembreCampagneDTO(
                id: $mc->getId() ?? 0,
                membreId: $membre?->getId() ?? 0,
                nomComplet: $nomComplet,
                telephone: $user?->getTelephone() ?? '',
                fonction: $membre?->getFonction()?->label(),
                initiales: $this->generateInitials($nomComplet),
                isLibre: (bool) $mc->isLibre(),
                modeAdhesion: $mc->isLibre() ? 'Montant Libre' : 'Part Forfaitaire',
                totalEpargneVerse: number_format($totalEpargne, 0, ',', ' ') . ' FCFA',
                totalCaisseVerse: number_format($totalCaisse, 0, ',', ' ') . ' FCFA',
                nombreAmendes: $nbAmendes,
                aAmendeImpayee: $aAmendeImpayee
            );
        }

        // Séances distinctes
        $seancesDTO = $this->getSeancesForCampagne($campagne);

        return [
            'campagne' => $campagneDTO,
            'participants' => $participantsDTO,
            'seances' => $seancesDTO,
        ];
    }

    public function getSeanceDetail(int $campagneId, string $dateRaw): ?array
    {
        $campagne = $this->campagneRepository->find($campagneId);
        if (!$campagne) {
            return null;
        }

        $targetDate = new \DateTimeImmutable($dateRaw);
        $dateFormatted = $targetDate->format('d/m/Y');
        $jourNom = $this->getFrenchDayName($targetDate->format('l'));

        $totalEpargne = 0.0;
        $totalCaisse = 0.0;
        $totalAmendes = 0.0;
        $nbPresents = 0;
        $nbAbsentsFonds = 0;
        $nbAbsentsNonPayes = 0;
        $cotisationsDTO = [];

        foreach ($campagne->getMembreCampagnes() as $mc) {
            $membre = $mc->getMembre();
            $user = $membre?->getCompte();
            $nomComplet = $user?->getNomComplet() ?? 'Adhérente';

            // Trouver la cotisation correspondante pour cette date
            $cotisationDuJour = null;
            foreach ($mc->getCotisations() as $cotis) {
                if ($cotis->getDateCotisationAt()?->format('Y-m-d') === $targetDate->format('Y-m-d')) {
                    $cotisationDuJour = $cotis;
                    break;
                }
            }

            // Trouver l'amende correspondante si existante
            $amendeDuJour = null;
            foreach ($mc->getAmendes() as $am) {
                if ($am->getDateDebutRetard()?->format('Y-m-d') === $targetDate->format('Y-m-d')) {
                    $amendeDuJour = $am;
                    break;
                }
            }

            if ($cotisationDuJour) {
                $statut = $cotisationDuJour->getStatutPresence();
                $epargne = (float) $cotisationDuJour->getMontantEpargne();
                $caisse = (float) $cotisationDuJour->getMontantCaisse();

                $totalEpargne += $epargne;
                $totalCaisse += $caisse;

                if ($statut === StatutPresence::PRESENT) {
                    $nbPresents++;
                } elseif ($statut === StatutPresence::ABSENT_AVEC_FONDS) {
                    $nbAbsentsFonds++;
                } else {
                    $nbAbsentsNonPayes++;
                }

                $amendeMontant = null;
                $amendeJours = null;
                $amendePayee = null;
                if ($amendeDuJour) {
                    $amendeMontant = number_format((float) $amendeDuJour->getMontantTotal(), 0, ',', ' ') . ' FCFA';
                    $amendeJours = $amendeDuJour->getNbJoursRetard();
                    $amendePayee = $amendeDuJour->isPayee();
                    $totalAmendes += (float) $amendeDuJour->getMontantTotal();
                }

                $cotisationsDTO[] = new CotisationItemDTO(
                    id: $cotisationDuJour->getId() ?? 0,
                    membreCampagneId: $mc->getId() ?? 0,
                    membreNom: $nomComplet,
                    telephone: $user?->getTelephone() ?? '',
                    initiales: $this->generateInitials($nomComplet),
                    fonction: $membre?->getFonction()?->label(),
                    statutPresence: $statut?->value ?? 'PRESENT',
                    statutPresenceLabel: $statut?->label() ?? 'Présente',
                    montantEpargne: number_format($epargne, 0, ',', ' ') . ' FCFA',
                    montantCaisse: number_format($caisse, 0, ',', ' ') . ' FCFA',
                    montantTotal: number_format($epargne + $caisse, 0, ',', ' ') . ' FCFA',
                    isLibre: (bool) $mc->isLibre(),
                    aAmende: $amendeDuJour !== null,
                    amendeMontant: $amendeMontant,
                    amendeJours: $amendeJours,
                    amendePayee: $amendePayee
                );
            }
        }

        $grandTotal = $totalEpargne + $totalCaisse + $totalAmendes;

        $seanceSummary = new SeanceSummaryDTO(
            dateRaw: $targetDate->format('Y-m-d'),
            dateFormatted: $dateFormatted,
            jourNom: $jourNom,
            totalEpargne: number_format($totalEpargne, 0, ',', ' ') . ' FCFA',
            totalCaisse: number_format($totalCaisse, 0, ',', ' ') . ' FCFA',
            totalAmendes: number_format($totalAmendes, 0, ',', ' ') . ' FCFA',
            grandTotal: number_format($grandTotal, 0, ',', ' ') . ' FCFA',
            nbPresents: $nbPresents,
            nbAbsentsAvecFonds: $nbAbsentsFonds,
            nbAbsentsNonPayes: $nbAbsentsNonPayes,
            totalMembres: count($cotisationsDTO),
            cotisations: $cotisationsDTO
        );

        return [
            'campagne' => $campagne,
            'seance' => $seanceSummary,
        ];
    }

    /**
     * @return list<SeanceSummaryDTO>
     */
    private function getSeancesForCampagne(Campagne $campagne): array
    {
        $dates = [];
        foreach ($campagne->getMembreCampagnes() as $mc) {
            foreach ($mc->getCotisations() as $cotis) {
                $d = $cotis->getDateCotisationAt()?->format('Y-m-d');
                if ($d && !in_array($d, $dates, true)) {
                    $dates[] = $d;
                }
            }
        }

        sort($dates);
        $seances = [];
        foreach ($dates as $dateRaw) {
            $detail = $this->getSeanceDetail($campagne->getId() ?? 0, $dateRaw);
            if ($detail && isset($detail['seance'])) {
                $seances[] = $detail['seance'];
            }
        }

        return $seances;
    }

    private function generateInitials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));
        $initials = '';
        foreach ($words as $w) {
            if (!empty($w)) {
                $initials .= mb_strtoupper(mb_substr($w, 0, 1));
            }
            if (mb_strlen($initials) >= 2) {
                break;
            }
        }

        return $initials ?: 'AD';
    }

    private function getFrenchDayName(string $englishDay): string
    {
        return match (strtolower($englishDay)) {
            'monday' => 'Lundi',
            'tuesday' => 'Mardi',
            'wednesday' => 'Mercredi',
            'thursday' => 'Jeudi',
            'friday' => 'Vendredi',
            'saturday' => 'Samedi',
            'sunday' => 'Dimanche',
            default => $englishDay,
        };
    }
}
