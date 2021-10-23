<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
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

        $product1 = (new Product())
            ->setName('Kindle Fire HD 7')
            ->setAuthor($defaultAuthor)
            ->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit.')
            ->setPrice('199.99')
            ->setCreatedAt(new \DateTime('-1 day -4 hours -3 minutes'))
            ->setIsPublished(true);

        $product2 = (new Product())
            ->setName('Samsung Galaxy S II')
            ->setDescription('Sed et velit suscipit nisi porttitor rutrum. Aliquam at ante justo, sed consectetur lorem.')
            ->setPrice('434.99')
            ->setIsPublished(true)
            ->setCreatedAt(new \DateTime('-1 month'));

        $product3 = (new Product())
            ->setName('Samsung 3D Slim LED')
            ->setDescription('Sed feugiat sem ac urna hendrerit ac sollicitudin est vulputate.')
            ->setPrice('2497.99')
            ->setIsPublished(false);

        $em->persist($product1);
        $em->persist($product2);
        $em->persist($product3);
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
}
