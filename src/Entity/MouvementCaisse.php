<?php

namespace App\Entity;

use App\Enum\SourceCaisse;
use App\Repository\MouvementCaisseRepository;
use App\Trait\DateTimeTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MouvementCaisseRepository::class)]
#[ORM\HasLifecycleCallbacks]
class MouvementCaisse
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30, enumType: SourceCaisse::class)]
    private ?SourceCaisse $source = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $montant = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $dateMouvement = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'mouvements', targetEntity: CaisseSociale::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?CaisseSociale $caisseSociale = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSource(): ?SourceCaisse
    {
        return $this->source;
    }

    public function setSource(SourceCaisse $source): static
    {
        $this->source = $source;

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

    public function getDateMouvement(): ?\DateTimeImmutable
    {
        return $this->dateMouvement;
    }

    public function setDateMouvement(\DateTimeImmutable $dateMouvement): static
    {
        $this->dateMouvement = $dateMouvement;

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

    public function getCaisseSociale(): ?CaisseSociale
    {
        return $this->caisseSociale;
    }

    public function setCaisseSociale(?CaisseSociale $caisseSociale): static
    {
        $this->caisseSociale = $caisseSociale;

        return $this;
    }
}
