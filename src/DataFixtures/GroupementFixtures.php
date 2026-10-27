<?php

namespace App\DataFixtures;

use App\Entity\Groupement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupementFixtures extends Fixture
{
    public const REFERENCE_PREFIX = 'groupement-';

    public function load(ObjectManager $manager): void
    {
        $donnees = [
            ['Groupement Solidarité', 'Épargne et entraide entre voisines du quartier.'],
            ['Association Entraide', 'Tontine mensuelle pour financer de petits commerces.'],
            ['Collectif Espoir', 'Soutien aux projets des jeunes entrepreneures.'],
            ['Union Progrès', 'Épargne collective pour la scolarité des enfants.'],
            ['Cercle Avenir', 'Groupement de cotisation pour les fêtes et cérémonies.'],
        ];

        foreach ($donnees as $index => [$nom, $description]) {
            $maintenant = new \DateTimeImmutable();

            $groupement = new Groupement();
            $groupement->setNom($nom);
            $groupement->setDescription($description);
            $groupement->setCreatedAt($maintenant);
            $groupement->setUpdatedAt($maintenant);

            $manager->persist($groupement);
            $this->addReference(self::REFERENCE_PREFIX . $index, $groupement);
        }

        $manager->flush();
    }
}