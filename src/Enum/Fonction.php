<?php

namespace App\Enum;

enum Fonction: string
{
    case CHEF = 'CHEF';
    case SECRETAIRE = 'SECRETAIRE';
    case TRESORIERE = 'TRESORIERE';

    public function label(): string
    {
        return match ($this) {
            self::CHEF => 'Présidente / Chef de groupement',
            self::SECRETAIRE => 'Secrétaire générale',
            self::TRESORIERE => 'Trésorière',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
