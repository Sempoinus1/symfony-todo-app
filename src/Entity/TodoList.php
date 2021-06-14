<?php

namespace App\Entity;

use App\Repository\TodoListRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TodoListRepository::class)
 */
class TodoList
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $owner;

    /**
     * @ORM\Column(type="boolean")
     */
    private $shareable;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?int
    {
        return $this->owner;
    }

    public function setOwner(int $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getShareable(): ?bool
    {
        return $this->shareable;
    }

    public function setShareable(bool $shareable): self
    {
        $this->shareable = $shareable;

        return $this;
    }
}
