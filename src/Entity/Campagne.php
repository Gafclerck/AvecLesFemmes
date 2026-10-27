<?php

namespace App\Entity;

use App\Repository\CampagneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampagneRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'epargne' => Epargne::class,
    'tontine' => Tontine::class,
])]
abstract class Campagne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateDebutAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateFinAt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 0)]
    private ?string $montant = null;

    /**
     * @var Collection<int, MembreCampagne>
     */
    #[ORM\OneToMany(targetEntity: MembreCampagne::class, mappedBy: 'campagne')]
    private Collection $membreCampagnes;

    #[ORM\ManyToOne(inversedBy: 'campagnes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Groupement $groupement = null;

    public function __construct()
    {
        $this->membreCampagnes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDebutAt(): ?\DateTimeImmutable
    {
        return $this->dateDebutAt;
    }

    public function setDateDebutAt(\DateTimeImmutable $dateDebutAt): static
    {
        $this->dateDebutAt = $dateDebutAt;

        return $this;
    }

    public function getDateFinAt(): ?\DateTimeImmutable
    {
        return $this->dateFinAt;
    }

    public function setDateFinAt(\DateTimeImmutable $dateFinAt): static
    {
        $this->dateFinAt = $dateFinAt;

        return $this;
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

    /**
     * @return Collection<int, MembreCampagne>
     */
    public function getMembreCampagnes(): Collection
    {
        return $this->membreCampagnes;
    }

    public function addMembreCampagne(MembreCampagne $membreCampagne): static
    {
        if (!$this->membreCampagnes->contains($membreCampagne)) {
            $this->membreCampagnes->add($membreCampagne);
            $membreCampagne->setCampagne($this);
        }

        return $this;
    }

    public function removeMembreCampagne(MembreCampagne $membreCampagne): static
    {
        if ($this->membreCampagnes->removeElement($membreCampagne)) {
            // set the owning side to null (unless already changed)
            if ($membreCampagne->getCampagne() === $this) {
                $membreCampagne->setCampagne(null);
            }
        }

        return $this;
    }

    public function getGroupement(): ?Groupement
    {
        return $this->groupement;
    }

    public function setGroupement(?Groupement $groupement): static
    {
        $this->groupement = $groupement;

        return $this;
    }
}
