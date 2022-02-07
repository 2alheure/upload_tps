<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Render;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Render|null find($id, $lockMode = null, $lockVersion = null)
 * @method Render|null findOneBy(array $criteria, array $orderBy = null)
 * @method Render[]    findAll()
 * @method Render[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RenderRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Render::class);
    }

    public function findRendersOfUser(User $user) {
        return $this->createQueryBuilder('r')
            ->where('r.promo = :promo')
            ->setParameter('promo', $user->getPromo())
            ->orderBy('ABS(DATE_DIFF(r.dateBegin, CURRENT_DATE()))', 'ASC')
            ->addOrderBy('DATE_DIFF(r.dateBegin, CURRENT_DATE())', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Render[] Returns an array of Render objects
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
    public function findOneBySomeField($value): ?Render
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
