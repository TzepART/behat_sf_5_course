<?php

declare(strict_types=1);

namespace App\Tests\Fixtures\Api\V1\Order\Create;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class OrderFixture extends Fixture implements DependentFixtureInterface
{
    public const DEVICE_ID = 'activated_smart_tv';
    public const DEVICE_TYPE = 'smart_tv';

    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference(UserFixture::class);

        $device = new Order();
        $device
            ->setOriginalId(self::DEVICE_ID)
            ->setType(self::DEVICE_TYPE)
            ->setOsVersion('0.0')
            ->setApplicationVersion('0.0.1')
            ->setPassMediaTicket('12340')
            ->updateSmartActivationData('012345')
            ->setUser($user)
        ;

        $manager->persist($device);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
