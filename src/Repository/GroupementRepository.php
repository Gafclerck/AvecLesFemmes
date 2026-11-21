<?php

namespace App\Repository;

use App\DTO\GroupSearchDTO;
use App\Entity\Groupement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Groupement>
 */
class GroupementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Groupement::class);
    }

    /**
     * Construit le QueryBuilder pour la recherche et la pagination de groupements.
     * Ne déclenche pas l'exécution de la requête pour permettre au PaginationService d'appliquer l'offset et la limite.
     */
    public function createSearchQueryBuilder(?GroupSearchDTO $search = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('g')
            ->leftJoin('g.caisseSociale', 'cs')
            ->leftJoin('g.membres', 'm')
            ->leftJoin('g.campagnes', 'c')
            ->leftJoin('g.exercices', 'e')
            ->addSelect('cs', 'm', 'c', 'e')
            ->orderBy('g.nom', 'ASC');

        if ($search !== null && $search->q !== null && trim($search->q) !== '') {
            $term = '%' . mb_strtolower(trim($search->q)) . '%';
            $qb->andWhere('LOWER(g.nom) LIKE :term OR LOWER(g.description) LIKE :term')
               ->setParameter('term', $term);
        }

        return $qb;
    }

//    /**
//     * @return Groupement[] Returns an array of Groupement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Groupement
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
