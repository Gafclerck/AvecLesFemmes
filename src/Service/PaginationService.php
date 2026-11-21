<?php

namespace App\Service;

use App\DTO\PageDTO;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Service générique de pagination Doctrine.
 * Évite la duplication de calculs d'offset, limit et total dans les contrôleurs.
 */
class PaginationService
{
    /**
     * Pagine les résultats d'un QueryBuilder Doctrine et encapsule le résultat dans un PageDTO.
     *
     * @template T of object
     * @param QueryBuilder $queryBuilder Requête non exécutée
     * @param int $page Numéro de page courante (1 par défaut)
     * @param int $limit Nombre d'éléments par page (5 par défaut)
     * @return PageDTO<T>
     */
    public function paginate(QueryBuilder $queryBuilder, int $page = 1, int $limit = 5): PageDTO
    {
        $page = max(1, $page);
        $limit = max(1, $limit);
        $offset = ($page - 1) * $limit;

        $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $paginator = new Paginator($queryBuilder, fetchJoinCollection: true);
        $totalElements = count($paginator);
        $elements = iterator_to_array($paginator);

        return new PageDTO(
            elements: $elements,
            pageEnCours: $page,
            limit: $limit,
            totalElements: $totalElements
        );
    }
}
