<?php

namespace App\Entity;

use App\Repository\PretRepository;
use App\Trait\DateTimeTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PretRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Pret
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $montant = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $datePretAt = null;

    #[ORM\Column]
    private ?int $nombreMois = null;

    #[ORM\Column]
    private ?float $interet = null;

    #[ORM\ManyToOne(inversedBy: 'prets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MembreCampagne $membreCampagne = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDatePretAt(): ?\DateTimeImmutable
    {
        return $this->datePretAt;
    }

    public function setDatePretAt(\DateTimeImmutable $datePretAt): static
    {
        $this->datePretAt = $datePretAt;

        return $this;
    }

    public function getNombreMois(): ?int
    {
        return $this->nombreMois;
    }

    public function setNombreMois(int $nombreMois): static
    {
        $this->nombreMois = $nombreMois;

        return $this;
    }

    public function getInteret(): ?float
    {
        return $this->interet;
    }

    public function setInteret(float $interet): static
    {
        $this->interet = $interet;

        return $this;
    }

    public function getMembreCampagne(): ?MembreCampagne
    {
        return $this->membreCampagne;
    }

    public function setMembreCampagne(?MembreCampagne $membreCampagne): static
    {
        $this->membreCampagne = $membreCampagne;

        return $this;
    }
}
