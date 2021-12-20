<?php

namespace App\Repository;

use App\Entity\LandPerClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LandPerClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method LandPerClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method LandPerClient[]    findAll()
 * @method LandPerClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LandPerClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LandPerClient::class);
    }

    // /**
    //  * @return LandPerClient[] Returns an array of LandPerClient objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LandPerClient
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
