<?php

namespace App\Repository;

use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


abstract class BaseRepository extends ServiceEntityRepository
{
    public function paginate(int $page, int $limit, string $alias, ?DateTime $date = null, ?string $orderBy = null): Paginator
    {
        $queryBuilder = $this->createQueryBuilder($alias)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        if ($date) {
            $queryBuilder
                ->andWhere("$alias.eventAt >= :date")
                ->setParameter('date', $date);
        }

        if ($orderBy) {
            $queryBuilder->orderBy("$alias.$orderBy", 'ASC');
        }

        return new Paginator(
            $queryBuilder->getQuery()->setHint(
                Paginator::HINT_ENABLE_DISTINCT,
                false
            )
        );
    }
}
