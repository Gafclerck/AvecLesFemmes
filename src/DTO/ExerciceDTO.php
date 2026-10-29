<?php

namespace App\DTO;

readonly class ExerciceDTO
{
    public function __construct(
        public int $id,
        public string $dateDebut,
        public string $dateFin,
        public ?string $dateLimitePret,
        public ?string $dateRetribution,
        public string $statut,
        public string $statutLabel,
        public bool $isEnCours,
        public string $labelPeriode
    ) {}
}
