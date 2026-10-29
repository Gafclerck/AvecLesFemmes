<?php

namespace App\Entity;

use App\Enum\Periodicite;
use App\Repository\CampagneRepository;
use App\Trait\DateTimeTrait;
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
#[ORM\HasLifecycleCallbacks]
abstract class Campagne
{
    use DateTimeTrait;

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

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private ?string $montant = null;

    #[ORM\Column(length: 30, enumType: Periodicite::class, nullable: true)]
    private ?Periodicite $periodicite = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $jourRencontre = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $montantFixe = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $montantCaissePresent = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $montantCaisseAbsent = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $amendeJournaliere = null;

    /**
     * @var Collection<int, MembreCampagne>
     */
    #[ORM\OneToMany(targetEntity: MembreCampagne::class, mappedBy: 'campagne')]
    private Collection $membreCampagnes;

    #[ORM\ManyToOne(inversedBy: 'campagnes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Groupement $groupement = null;

    #[ORM\ManyToOne(inversedBy: 'campagnes', targetEntity: Exercice::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Exercice $exercice = null;

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

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->dateDebutAt;
    }

    public function setDateDebut(\DateTimeImmutable $dateDebut): static
    {
        $this->dateDebutAt = $dateDebut;

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

    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->dateFinAt;
    }

    public function setDateFin(\DateTimeImmutable $dateFin): static
    {
        $this->dateFinAt = $dateFin;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string|float $montant): static
    {
        $this->montant = (string) $montant;

        return $this;
    }

    public function getPeriodicite(): ?Periodicite
    {
        return $this->periodicite;
    }

    public function setPeriodicite(?Periodicite $periodicite): static
    {
        $this->periodicite = $periodicite;

        return $this;
    }

    public function getJourRencontre(): ?string
    {
        return $this->jourRencontre;
    }

    public function setJourRencontre(?string $jourRencontre): static
    {
        $this->jourRencontre = $jourRencontre;

        return $this;
    }

    public function getMontantFixe(): ?string
    {
        return $this->montantFixe;
    }

    public function setMontantFixe(string|float|null $montantFixe): static
    {
        $this->montantFixe = $montantFixe !== null ? (string) $montantFixe : null;

        return $this;
    }

    public function getMontantCaissePresent(): ?string
    {
        return $this->montantCaissePresent;
    }

    public function setMontantCaissePresent(string|float|null $montantCaissePresent): static
    {
        $this->montantCaissePresent = $montantCaissePresent !== null ? (string) $montantCaissePresent : null;

        return $this;
    }

    public function getMontantCaisseAbsent(): ?string
    {
        return $this->montantCaisseAbsent;
    }

    public function setMontantCaisseAbsent(string|float|null $montantCaisseAbsent): static
    {
        $this->montantCaisseAbsent = $montantCaisseAbsent !== null ? (string) $montantCaisseAbsent : null;

        return $this;
    }

    public function getAmendeJournaliere(): ?string
    {
        return $this->amendeJournaliere;
    }

    public function setAmendeJournaliere(string|float|null $amendeJournaliere): static
    {
        $this->amendeJournaliere = $amendeJournaliere !== null ? (string) $amendeJournaliere : null;

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

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): static
    {
        $this->exercice = $exercice;

        return $this;
    }
}
