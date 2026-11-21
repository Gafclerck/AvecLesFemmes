<?php

namespace App\Service;

/**
 * Service utilitaire statique dédié au formatage et calcul sur les dates.
 * Respecte le principe SRP (Single Responsibility Principle) en déchargeant les DTOs
 * de la logique temporelle.
 */
final class DateService
{
    private function __construct()
    {
        // Classe utilitaire statique non instanciable
    }

    public static function formatDate(?\DateTimeInterface $date, string $format = 'd/m/Y'): string
    {
        if ($date === null) {
            return '';
        }

        return $date->format($format);
    }

    /**
     * Calcule le nombre de jours restants entre aujourd'hui et une date cible (ex: dateLimitePret).
     * Retourne :
     *  - null si la date cible est absente
     *  - nombre positif de jours restants
     *  - 0 si c'est aujourd'hui
     *  - nombre négatif si la date est dépassée
     */
    public static function calculateDaysRemaining(?\DateTimeInterface $targetDate, ?\DateTimeInterface $from = null): ?int
    {
        if ($targetDate === null) {
            return null;
        }

        $now = $from ? \DateTimeImmutable::createFromInterface($from) : new \DateTimeImmutable('today');
        $target = \DateTimeImmutable::createFromInterface($targetDate)->setTime(0, 0, 0);
        $today = $now->setTime(0, 0, 0);

        $diff = $today->diff($target);

        return $diff->invert ? -(int) $diff->days : (int) $diff->days;
    }
}
