<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $defaultAuthor = $this->loadUsers($manager);
        $this->loadProducts($defaultAuthor, $manager);

        $manager->flush();
    }

    private function loadProducts(User $defaultAuthor, ObjectManager $em): void
    {
        $em->clear(Product::class);

        $products = [
            'Kindle Fire HD 7',
            'Samsung Galaxy S II',
            'Samsung 3D Slim LED',
        ];

        foreach ($products as $product) {
            $this->buildProduct($em, $product, $defaultAuthor);
        }

        $em->flush();
    }


    private function loadUsers(ObjectManager $em): User
    {
        $em->clear(Product::class);

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

        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * @param ObjectManager $em
     * @param string $name
     * @param User $defaultAuthor
     */
    private function buildProduct(ObjectManager $em, string $name, User $defaultAuthor): void
    {
        $product1 = (new Product())
            ->setName($name)
            ->setAuthor($defaultAuthor)
            ->setDescription($this->faker->text())
            ->setPrice($this->faker->numberBetween(100, 1000))
            ->setCreatedAt($this->faker->dateTimeBetween())
            ->setIsPublished($this->faker->boolean);

        $em->persist($product1);
    }
}
