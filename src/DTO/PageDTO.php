<?php

namespace App\DTO;

/**
 * DTO générique pour encapsuler les résultats d'une pagination.
 * Fournit les métadonnées de pagination et les méthodes d'aide à la navigation.
 *
 * @template T
 */
readonly class PageDTO
{
    public int $nombreDePages;

    /**
     * @param list<T> $elements
     */
    public function __construct(
        public array $elements,
        public int $pageEnCours,
        public int $limit,
        public int $totalElements
    ) {
        $this->nombreDePages = $this->limit > 0
            ? max(1, (int) ceil($this->totalElements / $this->limit))
            : 1;
    }

    public function hasPrevious(): bool
    {
        return $this->pageEnCours > 1;
    }

    public function hasNext(): bool
    {
        return $this->pageEnCours < $this->nombreDePages;
    }

    /**
     * Permet de transformer la collection d'éléments (ex: Entités -> DTOs) via un callable array_map.
     *
     * @template R
     * @param callable(T): R $callback
     * @return self<R>
     */
    public function map(callable $callback): self
    {
        return new self(
            elements: array_values(array_map($callback, $this->elements)),
            pageEnCours: $this->pageEnCours,
            limit: $this->limit,
            totalElements: $this->totalElements
        );
    }
}
