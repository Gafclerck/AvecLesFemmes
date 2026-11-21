<?php

namespace App\Service;

use App\DTO\CampagneDTO;
use App\DTO\ExerciceDTO;
use App\DTO\GroupSearchDTO;
use App\DTO\GroupementDetailDTO;
use App\DTO\GroupementListItemDTO;
use App\DTO\MembreDTO;
use App\DTO\PageDTO;
use App\Entity\Exercice;
use App\Entity\Groupement;
use App\Enum\StatutExercice;
use App\Repository\GroupementRepository;

class GroupementService
{
    public function __construct(
        private readonly GroupementRepository $groupementRepository,
        private readonly PaginationService $paginationService
    ) {}

    /**
     * @return PageDTO<GroupementListItemDTO>
     */
    public function getPaginatedGroupements(?GroupSearchDTO $search = null, int $page = 1, int $limit = 6): PageDTO
    {
        $qb = $this->groupementRepository->createSearchQueryBuilder($search);
        $pageEntities = $this->paginationService->paginate($qb, $page, $limit);

        return $pageEntities->map(fn(Groupement $g) => $this->mapGroupementToListItemDTO($g));
    }

    /**
     * @return list<GroupementListItemDTO>
     */
    public function getGroupementsForListing(): array
    {
        $groupements = $this->groupementRepository->findAll();

        return array_map(fn(Groupement $g) => $this->mapGroupementToListItemDTO($g), $groupements);
    }

    public function getGlobalStats(): array
    {
        $groupements = $this->groupementRepository->findAll();
        $totalCaisse = 0.0;
        $totalMembres = 0;
        $totalCampagnes = 0;

        foreach ($groupements as $g) {
            $caisse = $g->getCaisseSociale();
            $totalCaisse += (float) ($caisse ? $caisse->getSoldeTotal() : 0);
            $totalMembres += count($g->getMembres());
            $totalCampagnes += count($g->getCampagnes());
        }

        return [
            'totalCaisse' => number_format($totalCaisse, 0, ',', ' ') . ' FCFA',
            'totalMembres' => $totalMembres,
            'totalCampagnes' => $totalCampagnes,
            'totalGroupements' => count($groupements),
        ];
    }

    private function mapGroupementToListItemDTO(Groupement $groupement): GroupementListItemDTO
    {
        $exerciceActif = $this->resolveExerciceEnCours($groupement);
        $exerciceDTO = $exerciceActif ? $this->mapExerciceToDTO($exerciceActif) : null;

        $caisse = $groupement->getCaisseSociale();
        $solde = $caisse ? $caisse->getSoldeTotal() : '0.00';

        return new GroupementListItemDTO(
            id: $groupement->getId() ?? 0,
            nom: $groupement->getNom() ?? '',
            description: $groupement->getDescription(),
            soldeCaisseSociale: number_format((float) $solde, 0, ',', ' ') . ' FCFA',
            nombreMembres: count($groupement->getMembres()),
            nombreCampagnes: count($groupement->getCampagnes()),
            exerciceEnCours: $exerciceDTO,
            initiales: $this->generateInitials($groupement->getNom() ?? '')
        );
    }

    public function getGroupementDetail(int $id): ?GroupementDetailDTO
    {
        $groupement = $this->groupementRepository->find($id);
        if (!$groupement) {
            return null;
        }

        $caisse = $groupement->getCaisseSociale();
        $exerciceActif = $this->resolveExerciceEnCours($groupement);

        // Mappage des membres
        $membresDTO = [];
        foreach ($groupement->getMembres() as $membre) {
            $user = $membre->getCompte();
            $nomComplet = $user?->getNomComplet() ?? 'Membre #' . $membre->getId();
            $tel = $user?->getTelephone() ?? '';
            $adresse = $user?->getAdresse();
            $fct = $membre->getFonction();

            $membresDTO[] = new MembreDTO(
                id: $membre->getId() ?? 0,
                nomComplet: $nomComplet,
                telephone: $tel,
                adresse: $adresse,
                fonction: $fct?->value,
                fonctionLabel: $fct?->label() ?? 'Membre active',
                isGroupOwner: (bool) $membre->isGroupeOwner(),
                initiales: $this->generateInitials($nomComplet),
                aJour: true
            );
        }

        // Mappage des campagnes
        $campagnesDTO = [];
        foreach ($groupement->getCampagnes() as $campagne) {
            $type = str_contains(strtolower($campagne::class), 'tontine') ? 'Tontine' : 'Épargne Calebasse';
            $per = $campagne->getPeriodicite();

            $campagnesDTO[] = new CampagneDTO(
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
                groupementId: $groupement->getId() ?? 0,
                groupementNom: $groupement->getNom() ?? '',
                exercicePeriode: $campagne->getExercice() ? $this->mapExerciceToDTO($campagne->getExercice())->labelPeriode : null,
                nombreMembres: count($campagne->getMembreCampagnes())
            );
        }

        // Mappage des exercices
        $exercicesDTO = [];
        foreach ($groupement->getExercices() as $ex) {
            $exercicesDTO[] = $this->mapExerciceToDTO($ex);
        }

        return new GroupementDetailDTO(
            id: $groupement->getId() ?? 0,
            nom: $groupement->getNom() ?? '',
            description: $groupement->getDescription(),
            soldeCaisseSociale: number_format((float) ($caisse?->getSoldeTotal() ?? 0), 0, ',', ' ') . ' FCFA',
            totalCotisationsCaisse: number_format((float) ($caisse?->getTotalCotisationsCaisse() ?? 0), 0, ',', ' ') . ' FCFA',
            totalAmendes: number_format((float) ($caisse?->getTotalAmendes() ?? 0), 0, ',', ' ') . ' FCFA',
            totalInterets: number_format((float) ($caisse?->getTotalInterets() ?? 0), 0, ',', ' ') . ' FCFA',
            totalLocations: number_format((float) ($caisse?->getTotalLocations() ?? 0), 0, ',', ' ') . ' FCFA',
            exerciceEnCours: $exerciceActif ? $this->mapExerciceToDTO($exerciceActif) : null,
            membres: $membresDTO,
            campagnes: $campagnesDTO,
            exercices: $exercicesDTO,
            initiales: $this->generateInitials($groupement->getNom() ?? '')
        );
    }

    private function resolveExerciceEnCours(Groupement $groupement): ?Exercice
    {
        foreach ($groupement->getExercices() as $exercice) {
            if ($exercice->getStatut() === StatutExercice::EN_COURS) {
                return $exercice;
            }
        }

        return null;
    }

    private function mapExerciceToDTO(Exercice $exercice): ExerciceDTO
    {
        $isEnCours = $exercice->getStatut() === StatutExercice::EN_COURS;
        $debut = DateService::formatDate($exercice->getDateDebut());
        $fin = DateService::formatDate($exercice->getDateFin());
        $label = sprintf('Exercice %s (%s - %s)', $exercice->getDateDebut()?->format('Y') ?? '', $debut, $fin);
        $joursRestants = DateService::calculateDaysRemaining($exercice->getDateLimitePret());

        return new ExerciceDTO(
            id: $exercice->getId() ?? 0,
            dateDebut: $debut,
            dateFin: $fin,
            dateLimitePret: DateService::formatDate($exercice->getDateLimitePret()) ?: null,
            dateRetribution: DateService::formatDate($exercice->getDateRetribution()) ?: null,
            statut: $exercice->getStatut()?->value ?? '',
            statutLabel: $exercice->getStatut()?->label() ?? 'En cours',
            isEnCours: $isEnCours,
            labelPeriode: $label,
            joursRestants: $joursRestants
        );
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

        return $initials ?: 'GP';
    }
}
