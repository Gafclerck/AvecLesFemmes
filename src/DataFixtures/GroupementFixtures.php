<?php

namespace App\DataFixtures;

use App\Entity\CaisseSociale;
use App\Entity\Groupement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupementFixtures extends Fixture
{
    public const REFERENCE_PREFIX = 'groupement-';
    public const CAISSE_PREFIX = 'caisse-';

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
            $groupement = new Groupement();
            $groupement->setNom($nom);
            $groupement->setDescription($description);

            $caisse = new CaisseSociale();
            $caisse->setGroupement($groupement);
            $caisse->setSoldeTotal('0.00');
            $caisse->setTotalCotisationsCaisse('0.00');
            $caisse->setTotalAmendes('0.00');
            $caisse->setTotalInterets('0.00');
            $caisse->setTotalLocations('0.00');

            $manager->persist($groupement);
            $manager->persist($caisse);

            $this->addReference(self::REFERENCE_PREFIX . $index, $groupement);
            $this->addReference(self::CAISSE_PREFIX . $index, $caisse);
        }

        $manager->flush();
    }
}
