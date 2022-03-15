<?php

declare(strict_types=1);

namespace App\Tests\Fixtures\Api\Auth\Login;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixture extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ){}

    public function load(ObjectManager $manager)
    {
        $plaintextPassword = 'qwe123';
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );

        $user->setUsername('user_1234')
            ->setPassword($hashedPassword)
            ->setPlainPassword($plaintextPassword);

        $manager->persist($user);
        $manager->flush();
    }
}
