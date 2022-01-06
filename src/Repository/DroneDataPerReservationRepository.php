<?php

namespace App\Repository;

use App\Entity\DroneDataPerReservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DroneDataPerReservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method DroneDataPerReservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method DroneDataPerReservation[]    findAll()
 * @method DroneDataPerReservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DroneDataPerReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DroneDataPerReservation::class);
    }

    // /**
    //  * @return DroneDataPerReservation[] Returns an array of DroneDataPerReservation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DroneDataPerReservation
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}