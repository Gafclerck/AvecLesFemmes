<?php

namespace App\DTO;

readonly class SeanceSummaryDTO
{
    /**
     * @param list<CotisationItemDTO> $cotisations
     */
    public function __construct(
        public string $dateRaw,
        public string $dateFormatted,
        public string $jourNom,
        public string $totalEpargne,
        public string $totalCaisse,
        public string $totalAmendes,
        public string $grandTotal,
        public int $nbPresents,
        public int $nbAbsentsAvecFonds,
        public int $nbAbsentsNonPayes,
        public int $totalMembres,
        public array $cotisations = []
    ) {}
}
