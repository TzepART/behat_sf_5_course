<?php

declare(strict_types=1);

namespace App\Tests\Fixtures\Api\V1\Order\Create;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserWithApiTokenFixture extends Fixture
{
    public function __construct(
        private string $apiToken,
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
            ->setPlainPassword($plaintextPassword)
            ->setApiToken($this->apiToken);

        $this->addReference(self::class, $user);

        $manager->persist($user);
        $manager->flush();
    }
}
