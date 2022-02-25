<?php

namespace App\Repository;

use App\Entity\BankOperationCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BankOperationCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method BankOperationCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method BankOperationCategory[]    findAll()
 * @method BankOperationCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BankOperationCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BankOperationCategory::class);
    }

    // /**
    //  * @return BankOperationCategory[] Returns an array of BankOperationCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BankOperationCategory
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * @throws NonUniqueResultException
     */
    public function getOneWithBankOperationsBySlug(string $slug)
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.bankOperations', 'bo')
            ->addSelect('bo')
            ->andWhere('b.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
