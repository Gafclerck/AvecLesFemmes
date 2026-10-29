<?php

namespace App\DataFixtures;

use App\Entity\Epargne;
use App\Entity\Exercice;
use App\Entity\Groupement;
use App\Entity\Tontine;
use App\Enum\Periodicite;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CampagneFixtures extends Fixture implements DependentFixtureInterface
{
    // Références : campagne-{indice du groupement}-tontine et campagne-{indice du groupement}-epargne
    public const REFERENCE_PREFIX = 'campagne-';

    public function load(ObjectManager $manager): void
    {
        // [indice du groupement, nom, début, fin, montant, fréquence de rotation en jours]
        $tontines = [
            [0, 'Tontine Tabaski 2026', '2026-01-01', '2026-12-31', '25000', 30],
            [1, 'Tontine Marché 2026', '2026-01-01', '2026-12-31', '15000', 15],
            [2, 'Tontine Hebdo 2026', '2026-01-01', '2026-12-31', '5000', 7],
            [3, 'Tontine Entrepreneures 2026', '2026-01-01', '2026-12-31', '50000', 30],
            [4, 'Tontine Solidaire 2026', '2026-01-01', '2026-12-31', '30000', 15],
        ];

        // [indice du groupement, nom, début, fin, montant, montant fixe]
        $epargnes = [
            [0, 'Épargne Calebasse Solidarité', '2026-01-01', '2026-12-31', '1000000', '2000.00'],
            [1, 'Épargne Calebasse Entraide', '2026-01-01', '2026-12-31', '2000000', '2500.00'],
            [2, 'Épargne Calebasse Espoir', '2026-01-01', '2026-12-31', '1500000', '2000.00'],
            [3, 'Épargne Calebasse Scolarité', '2026-01-01', '2026-12-31', '800000', '1500.00'],
            [4, 'Épargne Calebasse Avenir', '2026-01-01', '2026-12-31', '3000000', '5000.00'],
        ];

        foreach ($tontines as [$g, $nom, $debut, $fin, $montant, $frequence]) {
            $groupement = $this->getReference(GroupementFixtures::REFERENCE_PREFIX . $g, Groupement::class);
            $exercice = $this->getReference(ExerciceFixtures::REFERENCE_PREFIX . $g . '-current', Exercice::class);

            $tontine = new Tontine();
            $tontine->setNom($nom);
            $tontine->setDateDebutAt(new \DateTimeImmutable($debut));
            $tontine->setDateFinAt(new \DateTimeImmutable($fin));
            $tontine->setMontant($montant);
            $tontine->setFrequenceRotation($frequence);
            $tontine->setGroupement($groupement);
            $tontine->setExercice($exercice);
            $tontine->setPeriodicite(Periodicite::MENSUEL);
            $tontine->setJourRencontre('SAMEDI');
            $tontine->setMontantFixe('5000.00');
            $tontine->setMontantCaissePresent('100.00');
            $tontine->setMontantCaisseAbsent('300.00');
            $tontine->setAmendeJournaliere('200.00');

            $manager->persist($tontine);
            $this->addReference(self::REFERENCE_PREFIX . $g . '-tontine', $tontine);
        }

        foreach ($epargnes as [$g, $nom, $debut, $fin, $montant, $montantFixe]) {
            $groupement = $this->getReference(GroupementFixtures::REFERENCE_PREFIX . $g, Groupement::class);
            $exercice = $this->getReference(ExerciceFixtures::REFERENCE_PREFIX . $g . '-current', Exercice::class);

            $epargne = new Epargne();
            $epargne->setNom($nom);
            $epargne->setDateDebutAt(new \DateTimeImmutable($debut));
            $epargne->setDateFinAt(new \DateTimeImmutable($fin));
            $epargne->setMontant($montant);
            $epargne->setGroupement($groupement);
            $epargne->setExercice($exercice);
            $epargne->setPeriodicite(Periodicite::HEBDOMADAIRE);
            $epargne->setJourRencontre('DIMANCHE');
            $epargne->setMontantFixe($montantFixe);
            $epargne->setMontantCaissePresent('100.00');
            $epargne->setMontantCaisseAbsent('300.00');
            $epargne->setAmendeJournaliere('350.00');

            $manager->persist($epargne);
            $this->addReference(self::REFERENCE_PREFIX . $g . '-epargne', $epargne);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            GroupementFixtures::class,
            ExerciceFixtures::class,
        ];
    }
}
