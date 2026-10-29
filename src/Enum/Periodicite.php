<?php

namespace App\Enum;

enum Periodicite: string
{
    case HEBDOMADAIRE = 'hebdomadaire';
    case BIMENSUEL = 'bimensuel';
    case MENSUEL = 'mensuel';
    case PAR_QUINZAINE = 'par_quinzaine';

    public function label(): string
    {
        return match ($this) {
            self::HEBDOMADAIRE => 'Hebdomadaire',
            self::BIMENSUEL => 'Bimensuel',
            self::MENSUEL => 'Mensuel',
            self::PAR_QUINZAINE => 'Par quinzaine',
        };
    }
}
