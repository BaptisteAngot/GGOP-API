<?php

namespace App\Repository;

use App\Entity\RiotServer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\This;

/**
 * @method RiotServer|null find($id, $lockMode = null, $lockVersion = null)
 * @method RiotServer|null findOneBy(array $criteria, array $orderBy = null)
 * @method RiotServer[]    findAll()
 * @method RiotServer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RiotServerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RiotServer::class);
    }

    // /**
    //  * @return RiotServer[] Returns an array of RiotServer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RiotServer
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
