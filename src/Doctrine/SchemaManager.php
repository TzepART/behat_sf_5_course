<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Handles rebuilding our database tables
 */
class SchemaManager
{
    public function __construct(
        private EntityManagerInterface $em
    ){}

    public function rebuildSchema(): void
    {
        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
        $tool = new SchemaTool($this->em);
        $tool->dropSchema($metadatas);
        $tool->updateSchema($metadatas, false);
    }
}
