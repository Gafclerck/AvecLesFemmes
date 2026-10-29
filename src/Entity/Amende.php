<?php

namespace App\Entity;

use App\Repository\AmendeRepository;
use App\Trait\DateTimeTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AmendeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Amende
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateDebutRetard = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateReglement = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $nbJoursRetard = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montantJournalier = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montantTotal = '0.00';

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private ?bool $isPayee = false;

    #[ORM\ManyToOne(inversedBy: 'amendes', targetEntity: MembreCampagne::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?MembreCampagne $membreCampagne = null;

    #[ORM\OneToOne(targetEntity: MouvementCaisse::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?MouvementCaisse $mouvementCaisse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebutRetard(): ?\DateTimeImmutable
    {
        return $this->dateDebutRetard;
    }

    public function setDateDebutRetard(\DateTimeImmutable $dateDebutRetard): static
    {
        $this->dateDebutRetard = $dateDebutRetard;

        return $this;
    }

    public function getDateReglement(): ?\DateTimeImmutable
    {
        return $this->dateReglement;
    }

    public function setDateReglement(?\DateTimeImmutable $dateReglement): static
    {
        $this->dateReglement = $dateReglement;

        return $this;
    }

    public function getNbJoursRetard(): ?int
    {
        return $this->nbJoursRetard;
    }

    public function setNbJoursRetard(int $nbJoursRetard): static
    {
        $this->nbJoursRetard = $nbJoursRetard;

        return $this;
    }

    public function getMontantJournalier(): ?string
    {
        return $this->montantJournalier;
    }

    public function setMontantJournalier(string|float $montantJournalier): static
    {
        $this->montantJournalier = (string) $montantJournalier;

        return $this;
    }

    public function getMontantTotal(): ?string
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(string|float $montantTotal): static
    {
        $this->montantTotal = (string) $montantTotal;

        return $this;
    }

    public function isPayee(): ?bool
    {
        return $this->isPayee;
    }

    public function setIsPayee(bool $isPayee): static
    {
        $this->isPayee = $isPayee;

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

    public function getMouvementCaisse(): ?MouvementCaisse
    {
        return $this->mouvementCaisse;
    }

    public function setMouvementCaisse(?MouvementCaisse $mouvementCaisse): static
    {
        $this->mouvementCaisse = $mouvementCaisse;

        return $this;
    }
}
