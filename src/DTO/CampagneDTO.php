<?php

namespace App\DTO;

readonly class CampagneDTO
{
    public function __construct(
        public int $id,
        public string $nom,
        public string $type,
        public string $dateDebut,
        public string $dateFin,
        public string $montant,
        public string $periodicite,
        public string $periodiciteLabel,
        public string $jourRencontre,
        public string $montantFixe,
        public string $montantCaissePresent,
        public string $montantCaisseAbsent,
        public string $amendeJournaliere,
        public int $groupementId,
        public string $groupementNom,
        public ?string $exercicePeriode,
        public int $nombreMembres = 0
    ) {}
}
