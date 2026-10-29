<?php

namespace App\DTO;

readonly class MembreDTO
{
    public function __construct(
        public int $id,
        public string $nomComplet,
        public string $telephone,
        public ?string $adresse,
        public ?string $fonction,
        public ?string $fonctionLabel,
        public bool $isGroupOwner,
        public string $initiales,
        public bool $aJour = true
    ) {}
}
