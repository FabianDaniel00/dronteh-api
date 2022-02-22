<?php

namespace App\Repository;

use App\Entity\AreaOfUseChemical;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AreaOfUseChemical|null find($id, $lockMode = null, $lockVersion = null)
 * @method AreaOfUseChemical|null findOneBy(array $criteria, array $orderBy = null)
 * @method AreaOfUseChemical[]    findAll()
 * @method AreaOfUseChemical[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AreaOfUseChemicalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AreaOfUseChemical::class);
    }

    // /**
    //  * @return AreaOfUseChemical[] Returns an array of AreaOfUseChemical objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AreaOfUseChemical
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
