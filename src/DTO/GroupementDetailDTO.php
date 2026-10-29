<?php

namespace App\DTO;

readonly class GroupementDetailDTO
{
    /**
     * @param list<MembreDTO> $membres
     * @param list<CampagneDTO> $campagnes
     * @param list<ExerciceDTO> $exercices
     */
    public function __construct(
        public int $id,
        public string $nom,
        public ?string $description,
        public string $soldeCaisseSociale,
        public string $totalCotisationsCaisse,
        public string $totalAmendes,
        public string $totalInterets,
        public string $totalLocations,
        public ?ExerciceDTO $exerciceEnCours,
        public array $membres,
        public array $campagnes,
        public array $exercices,
        public string $initiales
    ) {}
}
