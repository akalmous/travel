<?php

namespace App\Repository;

use App\Entity\Trip;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Trip>
 *
 * @method Trip|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trip|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trip[]    findAll()
 * @method Trip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    public function save(Trip $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Trip $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    

//    /**
//     * @return Trip[] Returns an array of Trip objects
//     */
    public function findByName($searchBar, $price ,  $dateDate)
    
    {
        
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where('t.name  LIKE :searchBar')
            ->orwhere('t.departurePlace lIKE :searchBar')

            
            ->setParameter('searchBar', '%' . $searchBar . '%');
            
            
            


            if ($price) {
            $qb->andWhere('t.price <= :price ')
            ->setParameter('price',  $price );
        }
            if ($dateDate) {
                $qb->andWhere('t.departureDate = :dateDate')
                ->setParameter('dateDate',  $dateDate );
            }
            
            


            
            

        ;
        echo $qb;
        echo $searchBar;
        echo $price;
        echo $dateDate;


        return $qb
            ->getQuery()
            ->getResult();

        // returns an array of Product objects
  
    }
    

//    public function findOneBySomeField($value): ?Trip
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
