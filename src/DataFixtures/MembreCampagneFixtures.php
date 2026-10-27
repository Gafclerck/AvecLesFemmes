<?php

namespace App\DataFixtures;

use App\Entity\Epargne;
use App\Entity\Membre;
use App\Entity\MembreCampagne;
use App\Entity\Tontine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MembreCampagneFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $classes = ['tontine' => Tontine::class, 'epargne' => Epargne::class];

        // Chaque membre est inscrit aux 2 campagnes de SON groupement :
        // 5 groupements x 2 campagnes x 3 membres = 30 lignes, sans doublon
        for ($g = 0; $g < 5; $g++) {
            foreach ($classes as $type => $classe) {
                $campagne = $this->getReference(CampagneFixtures::REFERENCE_PREFIX . $g . '-' . $type, $classe);

                for ($position = 0; $position < 3; $position++) {
                    $index = $g * 3 + $position;

                    $membreCampagne = new MembreCampagne();
                    $membreCampagne->setMembre($this->getReference(MembreFixtures::REFERENCE_PREFIX . $index, Membre::class));
                    $membreCampagne->setCampagne($campagne);
                    // Alternance pour avoir des true et des false
                    $membreCampagne->setIsLibre($index % 2 === 0);

                    $manager->persist($membreCampagne);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [MembreFixtures::class, CampagneFixtures::class];
    }
}
