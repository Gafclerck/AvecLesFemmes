<?php

namespace App\Entity;

use App\Repository\CotisationRepository;
use App\Trait\DateTimeTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CotisationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Cotisation
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $montant = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCotisationAt = null;

    #[ORM\ManyToOne(inversedBy: 'cotisations')]
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

    public function getDateCotisationAt(): ?\DateTimeImmutable
    {
        return $this->dateCotisationAt;
    }

    public function setDateCotisationAt(\DateTimeImmutable $dateCotisationAt): static
    {
        $this->dateCotisationAt = $dateCotisationAt;

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
