<?php

declare(strict_types=1);

namespace App\Doctrine;

use App\DataFixtures\AppFixtures;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Handles rebuilding our database tables
 */
class SchemaManager
{
    /**
     * @var EntityManager
     */
    private EntityManagerInterface $em;
    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->em = $em;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function rebuildSchema(): void
    {
        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
        $tool = new SchemaTool($this->em);
        $tool->dropSchema($metadatas);
        $tool->updateSchema($metadatas, false);
    }

    public function loadFixtures(): void
    {
        $fixture = new AppFixtures($this->userPasswordHasher);
        $fixture->load($this->em);
    }
}
