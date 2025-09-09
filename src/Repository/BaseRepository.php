<?php

namespace App\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


abstract class BaseRepository extends ServiceEntityRepository
{
    public function paginate(int $page, int $limit, string $alias): Paginator
    {
        return new Paginator(
            $this->createQueryBuilder($alias)
                ->setFirstResult(($page - 1) * $limit)
                ->setMaxResults($limit)
                ->getQuery()
                ->setHint(
                    Paginator::HINT_ENABLE_DISTINCT,
                    false
                )
        );
    }
}
