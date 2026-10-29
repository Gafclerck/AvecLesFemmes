<?php

namespace App\DataFixtures;

use App\Entity\Amende;
use App\Entity\CaisseSociale;
use App\Entity\Cotisation;
use App\Entity\Groupement;
use App\Entity\MembreCampagne;
use App\Entity\MouvementCaisse;
use App\Enum\SourceCaisse;
use App\Enum\StatutPresence;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeanceCotisationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($g = 0; $g < 5; $g++) {
            $caisse = $this->getReference(GroupementFixtures::CAISSE_PREFIX . $g, CaisseSociale::class);
            $adhesion0 = $this->getReference(MembreCampagneFixtures::REFERENCE_PREFIX . $g . '-epargne-0', MembreCampagne::class);
            $adhesion1 = $this->getReference(MembreCampagneFixtures::REFERENCE_PREFIX . $g . '-epargne-1', MembreCampagne::class);
            $adhesion2 = $this->getReference(MembreCampagneFixtures::REFERENCE_PREFIX . $g . '-epargne-2', MembreCampagne::class);

            $campagne = $adhesion0->getCampagne();
            $montantEpargne = (float) $campagne->getMontantFixe();
            $caissePresent = (float) $campagne->getMontantCaissePresent();
            $caisseAbsent = (float) $campagne->getMontantCaisseAbsent();
            $amendeJour = (float) $campagne->getAmendeJournaliere();

            $totalCotisationsCaisse = 0.0;
            $totalAmendes = 0.0;

            // --- Séance 1 : 11 Janvier 2026 ---
            $dateSeance1 = new \DateTimeImmutable('2026-01-11 16:00:00');

            // Membre 0 : Présente
            $cotis1_0 = (new Cotisation())
                ->setMembreCampagne($adhesion0)
                ->setDateCotisation($dateSeance1)
                ->setMontantEpargne($montantEpargne)
                ->setMontantCaisse($caissePresent)
                ->setStatutPresence(StatutPresence::PRESENT);
            $manager->persist($cotis1_0);

            // Membre 1 : Présente
            $cotis1_1 = (new Cotisation())
                ->setMembreCampagne($adhesion1)
                ->setDateCotisation($dateSeance1)
                ->setMontantEpargne($montantEpargne)
                ->setMontantCaisse($caissePresent)
                ->setStatutPresence(StatutPresence::PRESENT);
            $manager->persist($cotis1_1);

            // Membre 2 : Absente avec fonds
            $cotis1_2 = (new Cotisation())
                ->setMembreCampagne($adhesion2)
                ->setDateCotisation($dateSeance1)
                ->setMontantEpargne($montantEpargne)
                ->setMontantCaisse($caisseAbsent)
                ->setStatutPresence(StatutPresence::ABSENT_AVEC_FONDS);
            $manager->persist($cotis1_2);

            $caisseSeance1 = $caissePresent + $caissePresent + $caisseAbsent;
            $totalCotisationsCaisse += $caisseSeance1;

            $mvt1 = (new MouvementCaisse())
                ->setCaisseSociale($caisse)
                ->setDateMouvement($dateSeance1)
                ->setSource(SourceCaisse::COTISATION_CAISSE)
                ->setMontant($caisseSeance1)
                ->setDescription('Cotisations caisse sociale - Séance du 11/01/2026');
            $manager->persist($mvt1);

            // --- Séance 2 : 18 Janvier 2026 ---
            $dateSeance2 = new \DateTimeImmutable('2026-01-18 16:00:00');

            // Membre 0 : Présente
            $cotis2_0 = (new Cotisation())
                ->setMembreCampagne($adhesion0)
                ->setDateCotisation($dateSeance2)
                ->setMontantEpargne($montantEpargne)
                ->setMontantCaisse($caissePresent)
                ->setStatutPresence(StatutPresence::PRESENT);
            $manager->persist($cotis2_0);

            // Membre 1 : Absente avec fonds
            $cotis2_1 = (new Cotisation())
                ->setMembreCampagne($adhesion1)
                ->setDateCotisation($dateSeance2)
                ->setMontantEpargne($montantEpargne)
                ->setMontantCaisse($caisseAbsent)
                ->setStatutPresence(StatutPresence::ABSENT_AVEC_FONDS);
            $manager->persist($cotis2_1);

            // Membre 2 : Absente non payée -> Déclenche amende
            $cotis2_2 = (new Cotisation())
                ->setMembreCampagne($adhesion2)
                ->setDateCotisation($dateSeance2)
                ->setMontantEpargne('0.00')
                ->setMontantCaisse('0.00')
                ->setStatutPresence(StatutPresence::ABSENT_NON_PAYE);
            $manager->persist($cotis2_2);

            $caisseSeance2 = $caissePresent + $caisseAbsent;
            $totalCotisationsCaisse += $caisseSeance2;

            $mvt2 = (new MouvementCaisse())
                ->setCaisseSociale($caisse)
                ->setDateMouvement($dateSeance2)
                ->setSource(SourceCaisse::COTISATION_CAISSE)
                ->setMontant($caisseSeance2)
                ->setDescription('Cotisations caisse sociale - Séance du 18/01/2026');
            $manager->persist($mvt2);

            // Amende réglée après 3 jours de retard
            $amendePayee = (new Amende())
                ->setMembreCampagne($adhesion2)
                ->setDateDebutRetard(new \DateTimeImmutable('2026-01-18'))
                ->setDateReglement(new \DateTimeImmutable('2026-01-21 11:00:00'))
                ->setNbJoursRetard(3)
                ->setMontantJournalier($amendeJour)
                ->setMontantTotal(3 * $amendeJour)
                ->setIsPayee(true);
            $manager->persist($amendePayee);

            $totalAmendes += (3 * $amendeJour);

            $mvtAmende = (new MouvementCaisse())
                ->setCaisseSociale($caisse)
                ->setDateMouvement(new \DateTimeImmutable('2026-01-21 11:00:00'))
                ->setSource(SourceCaisse::AMENDE)
                ->setMontant(3 * $amendeJour)
                ->setDescription('Paiement amende retard 3 jours - Séance 18/01/2026');
            $manager->persist($mvtAmende);
            $amendePayee->setMouvementCaisse($mvtAmende);

            // --- Séance 3 : 25 Janvier 2026 ---
            $dateSeance3 = new \DateTimeImmutable('2026-01-25 16:00:00');

            // Membre 0 : Présente
            $cotis3_0 = (new Cotisation())
                ->setMembreCampagne($adhesion0)
                ->setDateCotisation($dateSeance3)
                ->setMontantEpargne($montantEpargne)
                ->setMontantCaisse($caissePresent)
                ->setStatutPresence(StatutPresence::PRESENT);
            $manager->persist($cotis3_0);

            // Membre 1 : Présente
            $cotis3_1 = (new Cotisation())
                ->setMembreCampagne($adhesion1)
                ->setDateCotisation($dateSeance3)
                ->setMontantEpargne($montantEpargne)
                ->setMontantCaisse($caissePresent)
                ->setStatutPresence(StatutPresence::PRESENT);
            $manager->persist($cotis3_1);

            // Membre 2 : Absente non payée -> Amende en cours non encore réglée
            $cotis3_2 = (new Cotisation())
                ->setMembreCampagne($adhesion2)
                ->setDateCotisation($dateSeance3)
                ->setMontantEpargne('0.00')
                ->setMontantCaisse('0.00')
                ->setStatutPresence(StatutPresence::ABSENT_NON_PAYE);
            $manager->persist($cotis3_2);

            $caisseSeance3 = $caissePresent + $caissePresent;
            $totalCotisationsCaisse += $caisseSeance3;

            $mvt3 = (new MouvementCaisse())
                ->setCaisseSociale($caisse)
                ->setDateMouvement($dateSeance3)
                ->setSource(SourceCaisse::COTISATION_CAISSE)
                ->setMontant($caisseSeance3)
                ->setDescription('Cotisations caisse sociale - Séance du 25/01/2026');
            $manager->persist($mvt3);

            $amendeNonPayee = (new Amende())
                ->setMembreCampagne($adhesion2)
                ->setDateDebutRetard(new \DateTimeImmutable('2026-01-25'))
                ->setNbJoursRetard(4)
                ->setMontantJournalier($amendeJour)
                ->setMontantTotal(4 * $amendeJour)
                ->setIsPayee(false);
            $manager->persist($amendeNonPayee);

            // Mise à jour de la caisse sociale
            $caisse->setTotalCotisationsCaisse($totalCotisationsCaisse);
            $caisse->setTotalAmendes($totalAmendes);
            $caisse->setSoldeTotal($totalCotisationsCaisse + $totalAmendes);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            GroupementFixtures::class,
            MembreCampagneFixtures::class,
        ];
    }
}
