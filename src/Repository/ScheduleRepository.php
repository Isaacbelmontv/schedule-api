<?php

namespace App\Repository;

use App\Entity\Schedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Schedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Schedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Schedule[]    findAll()
 * @method Schedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Schedule::class);
    }

    public function save(Schedule $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Schedule $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByDay(string $day, bool $includeInactive = false): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.day = :day')
            ->setParameter('day', strtolower($day))
            ->orderBy('s.startTime', 'ASC');

        if (!$includeInactive) {
            $now = new \DateTime();
            $qb->andWhere('s.startTime >= :now')
               ->setParameter('now', $now->format('H:i:s'));
        }

        return $qb->getQuery()->getResult();
    }

    public function findUpcomingSchedules(int $limit = 10): array
    {
        $now = new \DateTime();
        $dayOfWeek = strtolower($now->format('l'));
        $currentTime = $now->format('H:i:s');

        return $this->createQueryBuilder('s')
            ->where('s.day = :today AND s.startTime >= :currentTime')
            ->orWhere('s.day != :today')
            ->setParameter('today', $dayOfWeek)
            ->setParameter('currentTime', $currentTime)
            ->orderBy('s.day', 'ASC')
            ->addOrderBy('s.startTime', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
