<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Event[] Returns an array of Event objects
     */
    public function findByOneParticipant(User $value): array
    {
        return $this->createQueryBuilder('e')
            ->join("e.participants", "p")
            ->andWhere('p = :val')
            ->setParameter('val', $value)
            ->orderBy('e.eventAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllAndMessages(): array
    {
        return $this->createQueryBuilder('e')
            ->join("e.messages", "m")
            ->addSelect('m')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Event[] Returns an array of Event objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
