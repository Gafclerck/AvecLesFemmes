<?php

namespace App\DTO;

readonly class GroupementListItemDTO
{
    public function __construct(
        public int $id,
        public string $nom,
        public ?string $description,
        public string $soldeCaisseSociale,
        public int $nombreMembres,
        public int $nombreCampagnes,
        public ?ExerciceDTO $exerciceEnCours,
        public string $initiales
    ) {}
}
