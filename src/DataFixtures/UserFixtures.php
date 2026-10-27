<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const REFERENCE_PREFIX = 'user-';
    public const ADMIN_REFERENCE = 'user-admin';

    // Mot de passe commun à tous les comptes de test (jamais stocké en clair)
    private const PASSWORD = 'password123';

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // Compte administrateur : n'est lié à aucun Membre
        $admin = new User();
        $admin->setTelephone('+221770000000');
        $admin->setNomComplet('Administrateur');
        $admin->setAdresse('Plateau, Dakar');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsActive(true);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, self::PASSWORD));
        $manager->persist($admin);
        $this->addReference(self::ADMIN_REFERENCE, $admin);

        // [téléphone, nom complet, adresse, actif]
        // L'indice dans ce tableau sert aussi à retrouver le compte (user-0 ... user-14)
        $comptes = [
            ['+221771100101', 'Aminata Diop', 'Médina, Dakar', true],
            ['+221772200202', 'Fatou Ndiaye', 'Pikine, Dakar', true],
            ['+221773300303', 'Khady Sow', 'Grand Yoff, Dakar', true],
            ['+221774400404', 'Awa Fall', 'Parcelles Assainies, Dakar', true],
            ['+221775500505', 'Mariama Ba', 'Guédiawaye', true],
            ['+221776600606', 'Rokhaya Sarr', 'Médina, Dakar', true],
            ['+221777700707', 'Ndeye Faye', 'Pikine, Dakar', true],
            ['+221778800808', 'Coumba Gueye', 'Grand Yoff, Dakar', false],
            ['+221779900909', 'Sokhna Diallo', 'Parcelles Assainies, Dakar', true],
            ['+221781101010', 'Adama Thiam', 'Guédiawaye', true],
            ['+221782202020', 'Bineta Cissé', 'Médina, Dakar', true],
            ['+221783303030', 'Mame Diarra Mbaye', 'Pikine, Dakar', true],
            ['+221784404040', 'Seynabou Ndoye', 'Grand Yoff, Dakar', true],
            ['+221785505050', 'Dieynaba Kane', 'Parcelles Assainies, Dakar', false],
            ['+221786606060', 'Astou Samb', 'Guédiawaye', true],
        ];

        foreach ($comptes as $index => [$telephone, $nomComplet, $adresse, $actif]) {
            $user = new User();
            $user->setTelephone($telephone);
            $user->setNomComplet($nomComplet);
            $user->setAdresse($adresse);
            $user->setIsActive($actif);
            $user->setPassword($this->passwordHasher->hashPassword($user, self::PASSWORD));

            $manager->persist($user);
            $this->addReference(self::REFERENCE_PREFIX . $index, $user);
        }

        $manager->flush();
    }
}
