<?php

namespace App\Enum;

enum SourceCaisse: string
{
    case COTISATION_CAISSE = 'COTISATION_CAISSE';
    case AMENDE = 'AMENDE';
    case INTERET = 'INTERET';
    case LOCATION = 'LOCATION';

    public function label(): string
    {
        return match ($this) {
            self::COTISATION_CAISSE => 'Cotisation solidaire de séance',
            self::AMENDE => 'Pénalités et amendes de retard',
            self::INTERET => 'Intérêts sur prêts remboursés',
            self::LOCATION => 'Locations de matériel du groupement',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
