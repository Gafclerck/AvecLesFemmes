<?php

namespace App\DataFixtures;

use App\Entity\Epargne;
use App\Entity\Groupement;
use App\Entity\Tontine;
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
            [0, 'Tontine Tabaski', '2025-03-01', '2026-02-28', '25000', 30],     // passée
            [1, 'Tontine Marché', '2026-03-01', '2027-02-28', '15000', 15],      // en cours
            [2, 'Tontine Hebdo', '2026-06-01', '2026-12-31', '5000', 7],         // en cours
            [3, 'Tontine Entrepreneures', '2027-02-01', '2027-11-30', '50000', 30], // à venir
            [4, 'Tontine Solidaire', '2026-09-01', '2027-08-31', '30000', 15],   // en cours
        ];

        // [indice du groupement, nom, début, fin, montant]
        $epargnes = [
            [0, 'Épargne Scolarité', '2026-01-01', '2026-12-31', '10000'],       // en cours
            [1, 'Épargne Projets', '2027-01-01', '2027-12-31', '20000'],         // à venir
            [2, 'Épargne Cérémonies', '2025-01-01', '2025-12-31', '12000'],      // passée
            [3, 'Épargne Santé', '2026-04-01', '2027-03-31', '8000'],            // en cours
            [4, 'Épargne Habitat', '2025-06-01', '2026-05-31', '40000'],         // passée
        ];

        foreach ($tontines as [$g, $nom, $debut, $fin, $montant, $frequence]) {
            $tontine = new Tontine();
            $tontine->setNom($nom);
            $tontine->setDateDebutAt(new \DateTimeImmutable($debut));
            $tontine->setDateFinAt(new \DateTimeImmutable($fin));
            $tontine->setMontant($montant);
            $tontine->setFrequenceRotation($frequence);
            $tontine->setGroupement($this->getReference(GroupementFixtures::REFERENCE_PREFIX . $g, Groupement::class));

            $manager->persist($tontine);
            $this->addReference(self::REFERENCE_PREFIX . $g . '-tontine', $tontine);
        }

        foreach ($epargnes as [$g, $nom, $debut, $fin, $montant]) {
            $epargne = new Epargne();
            $epargne->setNom($nom);
            $epargne->setDateDebutAt(new \DateTimeImmutable($debut));
            $epargne->setDateFinAt(new \DateTimeImmutable($fin));
            $epargne->setMontant($montant);
            $epargne->setGroupement($this->getReference(GroupementFixtures::REFERENCE_PREFIX . $g, Groupement::class));

            $manager->persist($epargne);
            $this->addReference(self::REFERENCE_PREFIX . $g . '-epargne', $epargne);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [GroupementFixtures::class];
    }
}
