<?php

namespace App\Repository;

use App\Entity\PartCamp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PartCamp>
 *
 * @method PartCamp|null find($id, $lockMode = null, $lockVersion = null)
 * @method PartCamp|null findOneBy(array $criteria, array $orderBy = null)
 * @method PartCamp[]    findAll()
 * @method PartCamp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartCampRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartCamp::class);
    }

//    /**
//     * @return PartCamp[] Returns an array of PartCamp objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PartCamp
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
