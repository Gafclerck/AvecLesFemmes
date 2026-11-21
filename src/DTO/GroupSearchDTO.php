<?php

namespace App\DTO;

/**
 * DTO encapsulant les critères de recherche pour les groupements.
 * Permet d'ajouter de futurs filtres sans modifier la signature du contrôleur.
 */
class GroupSearchDTO
{
    public function __construct(
        public ?string $q = null
    ) {}
}
