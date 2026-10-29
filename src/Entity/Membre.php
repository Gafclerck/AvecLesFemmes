<?php

namespace App\Entity;

use App\Enum\Fonction;
use App\Repository\MembreRepository;
use App\Trait\DateTimeTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MembreRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Membre
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true, enumType: Fonction::class)]
    private ?Fonction $fonction = null;

    #[ORM\Column]
    private ?bool $isGroupeOwner = false;

    #[ORM\OneToOne(inversedBy: 'membre', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $compte = null;

    /**
     * @var Collection<int, MembreCampagne>
     */
    #[ORM\OneToMany(targetEntity: MembreCampagne::class, mappedBy: 'membre')]
    private Collection $membreCampagnes;

    #[ORM\ManyToOne(inversedBy: 'membres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Groupement $groupement = null;

    public function __construct()
    {
        $this->membreCampagnes = new ArrayCollection();
        $this->isGroupeOwner = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFonction(): ?Fonction
    {
        return $this->fonction;
    }

    public function setFonction(Fonction|string|null $fonction): static
    {
        if (is_string($fonction)) {
            $this->fonction = Fonction::tryFrom($fonction);
        } else {
            $this->fonction = $fonction;
        }

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

    // Alias for schema compatibility
    public function isGroupOwner(): ?bool
    {
        return $this->isGroupeOwner();
    }

    public function setIsGroupOwner(bool $isGroupOwner): static
    {
        return $this->setIsGroupeOwner($isGroupOwner);
    }

    public function getCompte(): ?User
    {
        return $this->compte;
    }

    public function setCompte(User $compte): static
    {
        $this->compte = $compte;

        return $this;
    }

    // Alias for user relation
    public function getUser(): ?User
    {
        return $this->getCompte();
    }

    public function setUser(User $user): static
    {
        return $this->setCompte($user);
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
            $membreCampagne->setMembre($this);
        }

        return $this;
    }

    public function removeMembreCampagne(MembreCampagne $membreCampagne): static
    {
        if ($this->membreCampagnes->removeElement($membreCampagne)) {
            // set the owning side to null (unless already changed)
            if ($membreCampagne->getMembre() === $this) {
                $membreCampagne->setMembre(null);
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
