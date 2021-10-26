<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private string $username;

    /**
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $plainPassword = null;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = ['ROLE_USER'];

    /**
     * @ORM\Column(type="datetime")
     */
    public \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \Datetime();
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getUserIdentifier(): int
    {
        return $this->getId();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getCreatedAt(): \Datetime
    {
        return $this->createdAt;
    }

    public function __toString(): string
    {
        return $this->username;
    }
}
