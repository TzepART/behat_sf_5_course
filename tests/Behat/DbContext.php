<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DbContext implements Context
{
    protected KernelInterface $kernel;
    protected EntityManager $em;
    protected ?Loader $loader = null;
    protected ORMPurger $purger;
    protected ORMExecutor $executor;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @BeforeScenario
     */
    public function prepareDatabase(): void
    {
        /** @phpstan-ignore-next-line */
        $this->em = $this->kernel->getContainer()->get('doctrine')->getManager();

        $this->purger = new ORMPurger($this->em);
        $this->purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);

        $this->executor = new ORMExecutor($this->em, $this->purger);

        $this->em->beginTransaction();
    }

    /**
     * @AfterScenario
     */
    public function rollback(): void
    {
        $this->em->rollback();
    }

    /**
     * @param array<string|AbstractFixture> $fixtures
     */
    public function addFixtures(array $fixtures): self
    {
        foreach ($fixtures as $fixture) {
            $this->addFixture($fixture);
        }

        return $this;
    }

    /**
     * @param string|AbstractFixture $fixture
     */
    public function addFixture($fixture): self
    {
        if (null === $this->loader) {
            $this->loader = new Loader();
        }

        if ($fixture instanceof AbstractFixture) {
            $this->loader->addFixture($fixture);
        } else {
            $this->loader->addFixture(new $fixture());
        }

        return $this;
    }

    public function fixturesLoad(): self
    {
        if ($this->loader instanceof Loader) {
            $this->executor->execute($this->loader->getFixtures(), true);
            $this->loader = null;
        }

        return $this;
    }

    /**
     * @psalm-param class-string $className
     * @param array<mixed> $filter
     */
    public function entityExists(string $className, array $filter): ?object
    {
        $entity = $this->em->getRepository($className)->findOneBy($filter);
        Assert::assertTrue($entity instanceof $className);

        return $entity;
    }

    /**
     * @psalm-param class-string $className
     * @param array<mixed> $filter
     */
    public function entityNotExists(string $className, array $filter): void
    {
        $entity = $this->em->getRepository($className)->findOneBy($filter);
        $foundMessage = sprintf(
            '%s found by filter %s',
            $className,
            json_encode($filter, JSON_UNESCAPED_UNICODE)
        );
        Assert::assertNull($entity, $foundMessage);
    }

    public function getUserPasswordHasher(): UserPasswordHasherInterface
    {
        return $this->kernel->getContainer()->get('security.command.user_password_hash');
    }
}
