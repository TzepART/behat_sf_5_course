<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class ProductFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        /** @var User $defaultAuthor */
        $defaultAuthor = $this->getReference(UserFixtures::ADMIN_USER_KEY);

        $products = [
            'Kindle Fire HD 7',
            'Samsung Galaxy S II',
            'Samsung 3D Slim LED',
        ];

        foreach ($products as $product) {
            $product1 = (new Product())
                ->setName($product)
                ->setAuthor($defaultAuthor)
                ->setDescription($this->faker->text())
                ->setPrice($this->faker->numberBetween(100, 1000))
                ->setCreatedAt($this->faker->dateTimeBetween())
                ->setIsPublished($this->faker->boolean());

            $manager->persist($product1);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }
}