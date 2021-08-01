<?php

namespace App\Repository;

use App\Entity\Jeziora;
use App\Entity\JezioraSearch;
use App\Entity\Oplaty;
use App\Entity\Region;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Jeziora|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jeziora|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jeziora[]    findAll()
 * @method Jeziora[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JezioraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jeziora::class);
    }

    // /**
    //  * @return Jeziora[] Returns an array of Jeziora objects
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
    public function findOneBySomeField($value): ?Jeziora
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    //Kwerenda wyszukująca wszystkie aktywne ogłoszenia
    public function findWszystkie(JezioraSearch $search): array
    {
        $query = $this->createQueryBuilder('j')
            ->andWhere('j.aktywny = true')
            ->select('j')
            ->orderBy('j.utworzono', 'DESC')
            // ->getQuery()
            // ->getResult()
        ;

        if ($search->getNazwaSearch()) {
            $query
                ->andWhere('j.nazwa LIKE :nazwaszukaj')
                ->setParameter('nazwaszukaj', '%' . $search->getNazwaSearch() . '%');
        }

        if ($search->getPowierzchniaSearch()) {
            $query
                ->andWhere('j.powierzchnia >= :powierzchniaszukaj')
                ->setParameter('powierzchniaszukaj', $search->getPowierzchniaSearch());
        }

        if ($search->getMiejscowoscSearch()) {
            $query
                ->andWhere('j.miejscowosc LIKE :miejscowoscszukaj')
                ->setParameter('miejscowoscszukaj', '%' . $search->getMiejscowoscSearch() . '%');
        }

        if ($search->getPomostySearch()) {
            $query
                ->andWhere('j.pomosty = :pomostyszukaj')
                ->setParameter('pomostyszukaj', '1');
        }

        if ($search->getLodzSearch()) {
            $query
                ->andWhere('j.lodz = :lodzszukaj')
                ->setParameter('lodzszukaj', '1');
        }

        if ($search->getKuszaSearch()) {
            $query
                ->andWhere('j.kusza = :kuszaszukaj')
                ->setParameter('kuszaszukaj', '1');
        }

        if ($search->getRegionSearch()) {
            $query
                ->innerJoin('j.region', 'r')
                ->andWhere('r.wojewodztwo = :regionszukaj')
                ->setParameter('regionszukaj', $search->getRegionSearch());
        }

        $query = $query->getQuery();

        return $query->execute();

    }

    //Kwerenda wyszukująca ostatnie 6 ogłoszeń
    public function findOstatnie()
    {
        return $this->createQueryBuilder('j')
            ->select('j')
            ->andWhere('j.aktywny = true')
            ->orderBy('j.utworzono', 'DESC')
            ->setMaxResults(6)  //ilość wyszukiwanych ogłoszeń
            //->orderBy('rand')   //losowe wybieranie
            ->getQuery()
            ->getResult()
        ;
    }

    //Kwerenda wyszukująca wszystkie ogłoszenia danego usera
    public function findUser()
    {
        return $this->createQueryBuilder('j')
            ->select('j')
            ->orderBy('j.utworzono', 'ASC')
            ->setMaxResults(6)  //ilość wyszukiwanych ogłoszeń
            ->getQuery()
            ->getResult()
        ;
    }

    // Kwerenda wyszukująca wszystkie ogłoszenaktywne jeziora wszystkich uzytkowników
    public function findLicznikJezior()
    {
        return $this->createQueryBuilder('j')
            ->select('u','count(j)')
            ->join(Users::class, 'u', 'WITH', 'j.users = u.id')
            ->andWhere('j.aktywny = true')
            ->groupBy('j.users')
            ->orderBy('u.nazwisko', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //Wszytskie jeziora danego użytkownika
    public function findWszystkieJezioraUsera($user)
    {
        return $this->createQueryBuilder('j')
            ->select('j')
            ->andWhere('j.users = :users')
            ->setParameter('users', $user)
            ->andWhere('j.aktywny = true')
            ->orderBy('j.utworzono', 'ASC')
            ->getQuery()
            ->getResult();
            ;
    }

    // Kwerenda wyszukująca wszystkie ogłoszen aktywne jeziora wszystkich uzytkowników
    public function findWszystkieJaezioraUseraOplaty()
    {
        return $this->createQueryBuilder('j')
            ->select('j')
            ->join(Users::class, 'u', 'WITH', 'j.users = u.id')
            ->andWhere('j.aktywny = true')
            ->groupBy('j.users')
            ->getQuery()
            ->getResult();
    }


    // Kwerenda wyszukująca wszystkie ogłoszen aktywne jeziora wszystkich uzytkowników
    public function findByWszystkieJezioraUseraOplaty($user)
    {
        return $this->createQueryBuilder('j')
            ->select('j')
            ->join(Oplaty::class, 'o', 'WITH', 'j.id = o.jezioro')
            ->select('o')
            ->andWhere('j.users = :users')
            ->setParameter('users', $user)
            ->andWhere('j.aktywny = true')
            ->groupBy('j.nazwa', 'o.rodzaj')
            ->getQuery()
            ->getResult();
        ;
    }

    // Kwerenda wyszukująca wszystkie ogłoszen aktywne jeziora wszystkich uzytkowników
    public function findByWszystkieJezioraUseraBezOplat($user)
    {
        // get an ExpressionBuilder instance, so that you
        $expr = $this->_em->getExpressionBuilder();

        // create a subquery in order to take all address records for a specified user id
        $sub = $this->_em->createQueryBuilder()
            ->select('j')
            ->from(Oplaty::class, 'o')
            ->where('o.jezioro = j.id');


        $qb = $this->_em->createQueryBuilder()
            ->select('j')
            ->from(Jeziora::class, 'j')
            ->andWhere($expr->not($expr->exists($sub->getDQL())))
            ->andWhere('j.aktywny = true')
            ->andWhere('j.users = :users')
            ->setParameter('users', $user)
            ->andWhere('j.aktywny = true');

        return $qb->getQuery()->getResult();
    }

    // Kwerenda wyszukująca wszystkie ogłoszen aktywne jeziora wszystkich uzytkowników z pozycji usera
    public function findByAdminWszystkieJezioraUseraBezOplat()
    {
        // get an ExpressionBuilder instance, so that you
        $expr = $this->_em->getExpressionBuilder();

        // create a subquery in order to take all address records for a specified user id
        $sub = $this->_em->createQueryBuilder()
            ->select('j')
            ->from(Oplaty::class, 'o')
            ->where('o.jezioro = j.id')
            ->andWhere('j.aktywny = true')
        ;

        $qb = $this->_em->createQueryBuilder()
            ->select('j')
            ->from(Jeziora::class, 'j')
            ->andWhere($expr->not($expr->exists($sub->getDQL())))
            #->andWhere('j.aktywny = true')
        ;

        return $qb->getQuery()->getResult();
    }

    
    //Kwerenda wyszukująca 6 ogłoszeń wybranych losowo, kopia findOstatnie()
    public function findByOstatnieLosowe()
    {

        $qb = $this->createQueryBuilder('j')
            ->select('j')
            ->andWhere('j.aktywny = true')
            ->setMaxResults(6)  //ilość wyszukiwanych ogłoszeń
            ->getQuery()
            ->getResult()
        ;

        shuffle($qb);
        
        return $qb;
    }

    //Kwerenda wyszukująca jeziora ze zdjęciami
    public function findByFotoAdmin()
    {
        return $this->createQueryBuilder('j')
            ->select('j')
            #->andWhere('j.aktywny = true')
            ->orderBy('j.nazwa', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    // Kwerenda wyszukująca jeziora z podziałem na województwa
    public function findByRegion()
    {
        return $this->createQueryBuilder('j')
        ->select('count(j)')
        ->leftJoin(Region::class, 'r', 'WITH', 'j.region = r.id')
        ->andWhere('j.aktywny = true')
        ->addSelect('r')
        ->orderBy('count(j.region)', 'ASC')
        ->groupBy('j.region')
        ->getQuery()
        ->getResult()
        ;
    }

    // Kwerenda wyszukująca jeziora z podziałem na województwa
    public function findByRegionNotActive()
    {
        return $this->createQueryBuilder('j')
            ->select('j', 'count(j.region)')
            ->andWhere('j.aktywny = true')
            //->join(Region::class, 'r', 'WITH', 'j.region = r.id')
            //->select('r')
            ->orderBy('j.region', 'ASC')
            ->groupBy('j.region')
            ->getQuery()
            ->getResult();
    }

    // Kwerenda wyszukująca jeziora ze zdjęciem systemowym
    public function findBySystemFoto()
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.aktywny = true')
            ->andWhere('j.brochureFilename = :foto')
            ->setParameter('foto', 'klaffer-2834677_960_720.jpg')
            ->getQuery()
            ->getResult();
    }

    // public function findxxx()
    // {
    //     $qb = $this->createQueryBuilder('d');
    //     $qb
    //         ->where(
    //             $qb->expr()->andX(
    //                 $qb->expr()->orX(
    //                     $qb->expr()->like('d.data', ':query'),  
    //                 ),
    //                 $qb->expr()->eq('d.status', ':status'),
    //                 $qb->expr()->eq('d.owner', ':owner'),
    //             ),
    //         )
    //         ->setParameter('query', $query . '%')
    //         ->setParameter('status', 'active')
    //         ->setParameter('owner', $user)
    //         ->orderBy('d.data', 'ASC')
    //     ;
    //     //dump($qb->getQuery()->getResult()); //pokazuje tablice w obiekcie
    //     return $qb->getQuery()->getResult();
    // }
    
}
