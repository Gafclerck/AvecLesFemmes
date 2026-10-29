<?php

namespace App\Enum;

enum StatutPresence: string
{
    case PRESENT = 'PRESENT';
    case ABSENT_AVEC_FONDS = 'ABSENT_AVEC_FONDS';
    case ABSENT_NON_PAYE = 'ABSENT_NON_PAYE';

    public function label(): string
    {
        return match ($this) {
            self::PRESENT => 'Présent physiquement',
            self::ABSENT_AVEC_FONDS => 'Absent (fonds envoyés par tiers)',
            self::ABSENT_NON_PAYE => 'Absent sans paiement (amende)',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
