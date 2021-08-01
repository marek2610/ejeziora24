<?php

namespace App\Repository;

use App\Entity\Jeziora;
use App\Entity\Oplaty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Oplaty|null find($id, $lockMode = null, $lockVersion = null)
 * @method Oplaty|null findOneBy(array $criteria, array $orderBy = null)
 * @method Oplaty[]    findAll()
 * @method Oplaty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OplatyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Oplaty::class);
    }

    public function findByOplatyJezioro($jezioroID)
    {
        return $this->createQueryBuilder('o')
            ->select('o')
            ->andWhere('o.jezioro = :jezioroID')
            ->setParameter('jezioroID', $jezioroID)
            ->orderBy('o.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByAdminOplaty()
    {
        return $this->createQueryBuilder('o')
            ->select('o')
            #->orderBy('o.id', 'ASC')
            #->orderBy('o.jezioro', 'ASC')
            ->orderBy('o.user', 'ASC')
            #->add('orderBy', 'o.user ASC, o.jezioro ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    // public function findByJezioro($user)
    // {
    //     return $this->createQueryBuilder('o')
    //         ->select('o')
    //         ->join(Jeziora::class, 'u', 'WITH', 'j.users = u.id')
    //         ->andWhere('o.user = :user')
    //         ->setParameter('user', $user)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    // /**
    //  * @return Oplaty[] Returns an array of Oplaty objects
    //  */
    // public function findByOplatyJezioro($jezioroID)
    // {
    //     return $this->createQueryBuilder('o')
    //         ->select('o')
    //         ->andWhere('o.jezioro = :jezioroID')
    //         ->setParameter('jezioroID', $jezioroID)
    //         ->orderBy('o.id', 'ASC')
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }
  

    /*
    public function findOneBySomeField($value): ?Oplaty
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
