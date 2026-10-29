<?php

namespace App\DTO;

readonly class CotisationItemDTO
{
    public function __construct(
        public int $id,
        public int $membreCampagneId,
        public string $membreNom,
        public string $telephone,
        public string $initiales,
        public ?string $fonction,
        public string $statutPresence,
        public string $statutPresenceLabel,
        public string $montantEpargne,
        public string $montantCaisse,
        public string $montantTotal,
        public bool $isLibre,
        public bool $aAmende = false,
        public ?string $amendeMontant = null,
        public ?int $amendeJours = null,
        public ?bool $amendePayee = null
    ) {}
}
