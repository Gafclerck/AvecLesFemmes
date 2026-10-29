<?php

namespace App\DataFixtures;

use App\Entity\Exercice;
use App\Entity\Groupement;
use App\Enum\StatutExercice;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ExerciceFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE_PREFIX = 'exercice-';

    public function load(ObjectManager $manager): void
    {
        for ($g = 0; $g < 5; $g++) {
            $groupement = $this->getReference(GroupementFixtures::REFERENCE_PREFIX . $g, Groupement::class);

            // 1. Exercice en cours (2026)
            $exerciceActif = new Exercice();
            $exerciceActif->setGroupement($groupement);
            $exerciceActif->setDateDebut(new \DateTimeImmutable('2026-01-01'));
            $exerciceActif->setDateFin(new \DateTimeImmutable('2026-12-31'));
            $exerciceActif->setDateLimitePret(new \DateTimeImmutable('2026-10-31'));
            $exerciceActif->setDateRetribution(new \DateTimeImmutable('2026-12-20'));
            $exerciceActif->setStatut(StatutExercice::EN_COURS);

            $manager->persist($exerciceActif);
            $this->addReference(self::REFERENCE_PREFIX . $g . '-current', $exerciceActif);

            // 2. Exercice précédent clôturé (2025)
            $exercicePrecedent = new Exercice();
            $exercicePrecedent->setGroupement($groupement);
            $exercicePrecedent->setDateDebut(new \DateTimeImmutable('2025-01-01'));
            $exercicePrecedent->setDateFin(new \DateTimeImmutable('2025-12-31'));
            $exercicePrecedent->setDateLimitePret(new \DateTimeImmutable('2025-10-31'));
            $exercicePrecedent->setDateRetribution(new \DateTimeImmutable('2025-12-20'));
            $exercicePrecedent->setStatut(StatutExercice::CLOTURE);

            $manager->persist($exercicePrecedent);
            $this->addReference(self::REFERENCE_PREFIX . $g . '-closed', $exercicePrecedent);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [GroupementFixtures::class];
    }
}
