<?php

namespace App\DTO;

readonly class MembreCampagneDTO
{
    public function __construct(
        public int $id,
        public int $membreId,
        public string $nomComplet,
        public string $telephone,
        public ?string $fonction,
        public string $initiales,
        public bool $isLibre,
        public string $modeAdhesion,
        public string $totalEpargneVerse,
        public string $totalCaisseVerse,
        public int $nombreAmendes,
        public bool $aAmendeImpayee = false
    ) {}
}
