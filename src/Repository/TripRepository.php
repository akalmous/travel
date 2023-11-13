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

    public function findTripGuide(){
        $qb = $this->createQueryBuilder('t');
        $qb->where("t.guide is not null ");
        $qb->andwhere("t.archived is null ");

        //echo $qb;
        return $qb
            ->getQuery()
            ->getResult();

            
    }


    //Barre de recherche
    public function findByName($searchBar){
        
        $qb = $this->createQueryBuilder('t');
        $qb->where("t.archived is null ");
        $qb->andwhere("t.guide is not null ");
        // filtrer par la barre de recherche soit par le nom du voyage ou le nom de la destination
        $qb->andwhere('t.departurePlace lIKE :searchBar or t.name LIKE :searchBar')
        //$qb->orWhere('t.name  LIKE :searchBar')
        ->setParameter('searchBar', '%' . $searchBar . '%');
       
        return $qb
            ->getQuery()
            ->getResult();
            
        }



//    /**
//     * @return Trip[] Returns an array of Trip objects
//     */
    public function findByFilter($searchBar, $price ,  $dateDate, $duration, $guide){
        
        $qb = $this->createQueryBuilder('t');
        $qb->where("t.archived is null ");
        $qb->andwhere("t.guide is not null ");
            

        //echo($price);
        //echo($searchBar.''. $dateDate);

            
            // filtrer par la barre de recherche soit par le nom du voyage ou le nom de la destination
            
            
            if($searchBar){
                $qb->andwhere('t.departurePlace lIKE :searchBar or t.name LIKE :searchBar')
                //$qb->orWhere('t.name  LIKE :searchBar')
                ->setParameter('searchBar', '%' . $searchBar . '%');
                ;
            }
            // filtrer par le prix

            if ($price) {
            $price = intval($price); //convert to int 
            $qb->andWhere("t.price < :price or t.price = :price")
            ->setParameter('price',  $price );
            }

            //filtrer par la date de départ
            if ($dateDate) {
                $qb->andWhere('t.departureDate = :dateDate')
                ->setParameter('dateDate',  $dateDate );
            }

            // filter par la durée du voyage

            if ($duration) {
                $duration = intval($duration);
                $qb->andWhere('t.duration < :duration or t.duration = :duration')
                ->setParameter('duration', $duration );
            }
            
            
            if ($guide) {
              
              $qb->andWhere('t.guide IN (:guide)')
                ->setParameter('guide',  $guide );
                # code...
           }
            
        

        


           
        
        // echo("      TEST!!");
        //var_dump($guide);
       // echo($qb);
        return $qb
            ->getQuery()
            ->getResult();

        // returns an array of Product objects
  
    }
    // pour le dashbord : recuperer les trips de cette année

  //    /**
//     * @return Trip[] Returns an array of Trip objects
//     */
    public function findByDateTrips(): array
    {
         // je récupre l'année en cours afin de filtrer tout les guides inscrits au cours de cette année
        $year = date("Y");
        $date = "$year-00-00";
        return $this->createQueryBuilder('t')
            ->andwhere("t.archived is null ")
            ->andWhere("t.creationDate > :date or t.creationDate = :date ")
            ->setParameter('date',  $date )
            //->orderBy('u.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
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
