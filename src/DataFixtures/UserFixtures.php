<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_KEY = 'admin_user';

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ){}

    public function load(ObjectManager $manager)
    {
        $plaintextPassword = 'admin';
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );

        $user->setUsername('admin')
            ->setPassword($hashedPassword)
            ->setPlainPassword($plaintextPassword)
            ->setRoles(['ROLE_ADMIN']);

        $this->addReference(self::ADMIN_USER_KEY, $user);
        $manager->persist($user);
        $manager->flush();
    }
}