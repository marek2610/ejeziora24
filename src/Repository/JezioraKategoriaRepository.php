<?php

namespace App\Repository;

use App\Entity\JezioraKategoria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JezioraKategoria|null find($id, $lockMode = null, $lockVersion = null)
 * @method JezioraKategoria|null findOneBy(array $criteria, array $orderBy = null)
 * @method JezioraKategoria[]    findAll()
 * @method JezioraKategoria[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JezioraKategoriaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JezioraKategoria::class);
    }

    // /**
    //  * @return JezioraKategoria[] Returns an array of JezioraKategoria objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?JezioraKategoria
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
