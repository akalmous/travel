<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findByDateUsers($role): array
    {
        $year = date("Y");
        $date = "$year-00-00";
        
         $qb = $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->andWhere("u.creationDate > :date or u.creationDate = :date ")
            ->setParameter('role', '%' . $role . '%')
            ->setParameter('date',  $date)
            //->orderBy('u.id', 'ASC')
            //->setMaxResults(10)
            

            
        ;

        
        return $qb
        ->getQuery()
        ->getResult();
        
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findByDateGuides($role): array
    {
         // je récupre l'année en cours afin de filtrer tout les guides inscrits au cours de cette année
        $year = date("Y");
        $date = "$year-00-00";
        //echo($date);
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->andWhere("u.creationDate > :date or u.creationDate = :date ")
            ->setParameter('date',   $date )
            ->setParameter('role', '%' . $role . '%')
            //->orderBy('u.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;

        
    }

    

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
