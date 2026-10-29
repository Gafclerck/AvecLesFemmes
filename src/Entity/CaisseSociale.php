<?php

namespace App\Entity;

use App\Repository\CaisseSocialeRepository;
use App\Trait\DateTimeTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CaisseSocialeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class CaisseSociale
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 14, scale: 2, options: ['default' => '0.00'])]
    private ?string $soldeTotal = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 14, scale: 2, options: ['default' => '0.00'])]
    private ?string $totalCotisationsCaisse = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 14, scale: 2, options: ['default' => '0.00'])]
    private ?string $totalAmendes = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 14, scale: 2, options: ['default' => '0.00'])]
    private ?string $totalInterets = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 14, scale: 2, options: ['default' => '0.00'])]
    private ?string $totalLocations = '0.00';

    #[ORM\OneToOne(inversedBy: 'caisseSociale', targetEntity: Groupement::class)]
    #[ORM\JoinColumn(nullable: false, unique: true)]
    private ?Groupement $groupement = null;

    /**
     * @var Collection<int, MouvementCaisse>
     */
    #[ORM\OneToMany(targetEntity: MouvementCaisse::class, mappedBy: 'caisseSociale', cascade: ['persist'])]
    private Collection $mouvements;

    public function __construct()
    {
        $this->mouvements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSoldeTotal(): ?string
    {
        return $this->soldeTotal;
    }

    public function setSoldeTotal(string|float $soldeTotal): static
    {
        $this->soldeTotal = (string) $soldeTotal;

        return $this;
    }

    public function getTotalCotisationsCaisse(): ?string
    {
        return $this->totalCotisationsCaisse;
    }

    public function setTotalCotisationsCaisse(string|float $totalCotisationsCaisse): static
    {
        $this->totalCotisationsCaisse = (string) $totalCotisationsCaisse;

        return $this;
    }

    public function getTotalAmendes(): ?string
    {
        return $this->totalAmendes;
    }

    public function setTotalAmendes(string|float $totalAmendes): static
    {
        $this->totalAmendes = (string) $totalAmendes;

        return $this;
    }

    public function getTotalInterets(): ?string
    {
        return $this->totalInterets;
    }

    public function setTotalInterets(string|float $totalInterets): static
    {
        $this->totalInterets = (string) $totalInterets;

        return $this;
    }

    public function getTotalLocations(): ?string
    {
        return $this->totalLocations;
    }

    public function setTotalLocations(string|float $totalLocations): static
    {
        $this->totalLocations = (string) $totalLocations;

        return $this;
    }

    public function getGroupement(): ?Groupement
    {
        return $this->groupement;
    }

    public function setGroupement(Groupement $groupement): static
    {
        $this->groupement = $groupement;

        return $this;
    }

    /**
     * @return Collection<int, MouvementCaisse>
     */
    public function getMouvements(): Collection
    {
        return $this->mouvements;
    }

    public function addMouvement(MouvementCaisse $mouvement): static
    {
        if (!$this->mouvements->contains($mouvement)) {
            $this->mouvements->add($mouvement);
            $mouvement->setCaisseSociale($this);
        }

        return $this;
    }

    public function removeMouvement(MouvementCaisse $mouvement): static
    {
        if ($this->mouvements->removeElement($mouvement)) {
            if ($mouvement->getCaisseSociale() === $this) {
                $mouvement->setCaisseSociale(null);
            }
        }

        return $this;
    }
}
