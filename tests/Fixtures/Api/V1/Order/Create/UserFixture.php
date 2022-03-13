<?php

declare(strict_types=1);

namespace App\Tests\Fixtures\Api\V1\Order\Create;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user
            ->setUsername('username');

        $this->addReference(self::class, $user);

        $manager->persist($user);
        $manager->flush();
    }
}
