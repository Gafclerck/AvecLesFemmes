<?php

namespace App\Entity;

use App\Repository\TontineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TontineRepository::class)]
class Tontine extends Campagne
{
    #[ORM\Column(nullable: true)]
    private ?int $frequenceRotation = null;

    public function getFrequenceRotation(): ?int
    {
        return $this->frequenceRotation;
    }

    public function setFrequenceRotation(?int $frequenceRotation): static
    {
        $this->frequenceRotation = $frequenceRotation;

        return $this;
    }
}
