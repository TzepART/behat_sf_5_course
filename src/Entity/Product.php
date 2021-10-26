<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(name="product")
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="User")
     */
    private ?User $author = null;

    /**
     * @ORM\Column(type="string")
     */
    private string $description;

    /**
     * @ORM\Column(type="float")
     */
    private int $price;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isPublished = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime  $createdAt;

    public function __construct()
    {
        $this->createdAt = new \Datetime();
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;
        return $this;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function getCreatedAt(): \Datetime
    {
        return $this->createdAt;
    }
}
