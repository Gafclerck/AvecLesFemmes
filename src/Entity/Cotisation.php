<?php

namespace App\Entity;

use App\Enum\StatutPresence;
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

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    private ?string $montantEpargne = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    private ?string $montantCaisse = '0.00';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $dateCotisationAt = null;

    #[ORM\Column(length: 30, enumType: StatutPresence::class, nullable: true)]
    private ?StatutPresence $statutPresence = null;

    #[ORM\ManyToOne(inversedBy: 'cotisations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MembreCampagne $membreCampagne = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantEpargne(): ?string
    {
        return $this->montantEpargne;
    }

    public function setMontantEpargne(string|float $montantEpargne): static
    {
        $this->montantEpargne = (string) $montantEpargne;

        return $this;
    }

    public function getMontantCaisse(): ?string
    {
        return $this->montantCaisse;
    }

    public function setMontantCaisse(string|float $montantCaisse): static
    {
        $this->montantCaisse = (string) $montantCaisse;

        return $this;
    }

    // Helper for backward compatibility
    public function getMontant(): ?string
    {
        return (string) ((float) $this->montantEpargne + (float) $this->montantCaisse);
    }

    public function setMontant(string|float $montant): static
    {
        $this->montantEpargne = (string) $montant;

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

    public function getDateCotisation(): ?\DateTimeImmutable
    {
        return $this->dateCotisationAt;
    }

    public function setDateCotisation(\DateTimeImmutable $dateCotisation): static
    {
        $this->dateCotisationAt = $dateCotisation;

        return $this;
    }

    public function getStatutPresence(): ?StatutPresence
    {
        return $this->statutPresence;
    }

    public function setStatutPresence(?StatutPresence $statutPresence): static
    {
        $this->statutPresence = $statutPresence;

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
