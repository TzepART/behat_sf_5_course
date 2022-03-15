<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function updateApiToken(int $userIdentifier, ?string $apiToken): void
    {
        $this->_em->createQueryBuilder()
            ->update(User::class, 'u')
            ->set('u.apiToken', ':apiToken')
            ->where('u.id = :userIdentifier')
            ->setParameter('apiToken', $apiToken)
            ->setParameter('userIdentifier', $userIdentifier)
            ->getQuery()
            ->execute();
    }

    public function loadUserByUsername($username)
    {
        $user = $this->findOneBy(['username' => $username]);

        if (!$user) {
            throw new UserNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class): bool
    {
        return $class === User::class;
    }
}