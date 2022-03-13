<?php

declare(strict_types=1);

namespace App\Tests\Fixtures\Api;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AuthenticatedUserFixture extends Fixture
{
    public const SUBSCRIBER_ID = '12345678901';
    public const REFERENCE_NAME = 'authenticated_user';

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user
            ->setUsername('authenticated')
            ->setPassMediaId('authenticated_passMediaId')
            ->setSubscriberId(self::SUBSCRIBER_ID);

        $this->addReference(self::REFERENCE_NAME, $user);

        $manager->persist($user);
        $manager->flush();
    }
}
