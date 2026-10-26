<?php

namespace App\Entity;

use App\Repository\MembreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MembreRepository::class)]
class Membre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $fonction = null;

    #[ORM\Column]
    private ?bool $isGroupeOwner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): static
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function isGroupeOwner(): ?bool
    {
        return $this->isGroupeOwner;
    }

    public function setIsGroupeOwner(bool $isGroupeOwner): static
    {
        $this->isGroupeOwner = $isGroupeOwner;

        return $this;
    }
}
