<?php

namespace App\Repository;

use App\Entity\Guide;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Guide>
 *
 * @method Guide|null find($id, $lockMode = null, $lockVersion = null)
 * @method Guide|null findOneBy(array $criteria, array $orderBy = null)
 * @method Guide[]    findAll()
 * @method Guide[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Guide::class);
    }

    public function save(Guide $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Guide $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    

//    /**
//     * @return Guide[] Returns an array of Guide objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findOneBySomeField($userId): ?Guide
    {
        return $this->createQueryBuilder('g')
            ->join('g.user','u')
            ->Where('g.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult()


        
        ;
    }
}
