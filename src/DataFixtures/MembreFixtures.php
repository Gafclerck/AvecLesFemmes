<?php

namespace App\DataFixtures;

use App\Entity\Groupement;
use App\Entity\Membre;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MembreFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE_PREFIX = 'membre-';

    // Les 3 membres de chaque groupement, dans cet ordre
    private const FONCTIONS = ['CHEF', 'SECRETAIRE', 'TRESORIERE'];

    public function load(ObjectManager $manager): void
    {
        // 5 groupements x 3 membres = 15 membres (membre-0 ... membre-14)
        // Les membres du groupement $g ont les indices 3*g, 3*g+1, 3*g+2
        for ($g = 0; $g < 5; $g++) {
            $groupement = $this->getReference(GroupementFixtures::REFERENCE_PREFIX . $g, Groupement::class);

            foreach (self::FONCTIONS as $position => $fonction) {
                $index = $g * 3 + $position;

                $membre = new Membre();
                $membre->setCompte($this->getReference(UserFixtures::REFERENCE_PREFIX . $index, User::class));
                $membre->setGroupement($groupement);
                $membre->setFonction($fonction);
                // Seul le CHEF est propriétaire du groupement
                $membre->setIsGroupeOwner($position === 0);

                $manager->persist($membre);
                $this->addReference(self::REFERENCE_PREFIX . $index, $membre);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class, GroupementFixtures::class];
    }
}
