<?php

namespace App\Entity;

use App\Repository\GroupementRepository;
use App\Trait\DateTimeTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupementRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Groupement
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Membre>
     */
    #[ORM\OneToMany(targetEntity: Membre::class, mappedBy: 'groupement')]
    private Collection $membres;

    /**
     * @var Collection<int, Campagne>
     */
    #[ORM\OneToMany(targetEntity: Campagne::class, mappedBy: 'groupement')]
    private Collection $campagnes;

    /**
     * @var Collection<int, Exercice>
     */
    #[ORM\OneToMany(targetEntity: Exercice::class, mappedBy: 'groupement', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $exercices;

    #[ORM\OneToOne(mappedBy: 'groupement', targetEntity: CaisseSociale::class, cascade: ['persist', 'remove'])]
    private ?CaisseSociale $caisseSociale = null;

    public function __construct()
    {
        $this->membres = new ArrayCollection();
        $this->campagnes = new ArrayCollection();
        $this->exercices = new ArrayCollection();
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

    /**
     * @return Collection<int, Membre>
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    public function addMembre(Membre $membre): static
    {
        if (!$this->membres->contains($membre)) {
            $this->membres->add($membre);
            $membre->setGroupement($this);
        }

        return $this;
    }

    public function removeMembre(Membre $membre): static
    {
        if ($this->membres->removeElement($membre)) {
            // set the owning side to null (unless already changed)
            if ($membre->getGroupement() === $this) {
                $membre->setGroupement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Campagne>
     */
    public function getCampagnes(): Collection
    {
        return $this->campagnes;
    }

    public function addCampagne(Campagne $campagne): static
    {
        if (!$this->campagnes->contains($campagne)) {
            $this->campagnes->add($campagne);
            $campagne->setGroupement($this);
        }

        return $this;
    }

    public function removeCampagne(Campagne $campagne): static
    {
        if ($this->campagnes->removeElement($campagne)) {
            // set the owning side to null (unless already changed)
            if ($campagne->getGroupement() === $this) {
                $campagne->setGroupement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Exercice>
     */
    public function getExercices(): Collection
    {
        return $this->exercices;
    }

    public function addExercice(Exercice $exercice): static
    {
        if (!$this->exercices->contains($exercice)) {
            $this->exercices->add($exercice);
            $exercice->setGroupement($this);
        }

        return $this;
    }

    public function removeExercice(Exercice $exercice): static
    {
        if ($this->exercices->removeElement($exercice)) {
            if ($exercice->getGroupement() === $this) {
                $exercice->setGroupement(null);
            }
        }

        return $this;
    }

    public function getCaisseSociale(): ?CaisseSociale
    {
        return $this->caisseSociale;
    }

    public function setCaisseSociale(?CaisseSociale $caisseSociale): static
    {
        // unset the owning side of the relation if necessary
        if ($caisseSociale === null && $this->caisseSociale !== null) {
            // CaisseSociale is owning side
        }

        // set the owning side of the relation if necessary
        if ($caisseSociale !== null && $caisseSociale->getGroupement() !== $this) {
            $caisseSociale->setGroupement($this);
        }

        $this->caisseSociale = $caisseSociale;

        return $this;
    }
}
