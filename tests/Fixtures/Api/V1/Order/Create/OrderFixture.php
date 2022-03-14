<?php

declare(strict_types=1);

namespace App\Tests\Fixtures\Api\V1\Order\Create;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Order;

final class OrderFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference(UserFixture::class);

        $order = new Order();
        $order
            ->setCode('order_code')
            ->setCustomer($user)
        ;

        $manager->persist($order);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
