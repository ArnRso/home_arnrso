<?php

namespace App\Repository;

use App\Entity\BankOperation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BankOperation|null find($id, $lockMode = null, $lockVersion = null)
 * @method BankOperation|null findOneBy(array $criteria, array $orderBy = null)
 * @method BankOperation[]    findAll()
 * @method BankOperation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BankOperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BankOperation::class);
    }

    /**
     * @return ArrayCollection
     */
    public function getUniqIdsList(): ArrayCollection
    {
        $qb = $this->createQueryBuilder('bank_operation');

        $uniqId = $qb
            ->select('bank_operation.uniqId')
            ->getQuery()
            ->getResult();

        $uniqIdList = new ArrayCollection();
        foreach ($uniqId as $item) {
            if (!$uniqIdList->contains($item['uniqId'])) {
                $uniqIdList->add($item['uniqId']);
            }
        }
        return $uniqIdList;
    }

    /**
     * @param array $searchValues
     * @return BankOperation[]
     */
    public function getBankOperations(array $searchValues = array()): array
    {
        $qb = $this->getBankOperationsQuery($searchValues);

        return $qb->getResult();
    }

    /**
     * @param array $searchValues
     * @return Query
     */
    public function getBankOperationsQuery(array $searchValues): Query
    {
        $qb = $this->createQueryBuilder('bank_operation');

        $qb
            ->select('bank_operation')
            ->leftJoin('bank_operation.bankOperationCategory', 'bank_operation_category');

        if (!empty($searchValues['category'])) {
            $qb
                ->andWhere('bank_operation_category.id = :category')
                ->setParameter('category', $searchValues['category']);
        }

        if (!empty($searchValues['label'])) {
            $qb
                ->andWhere('bank_operation.label LIKE :label')
                ->setParameter('label', '%' . $searchValues['label'] . '%');
        }

        if (!empty($searchValues['sign'])) {
            if ($searchValues['sign'] === 'plus') {
                $qb->andWhere('bank_operation.amount >= 0');
            } elseif ($searchValues['sign'] === 'minus') {
                $qb->andWhere('bank_operation.amount <= 0');
            }
        }

        if (!empty($searchValues['dateFrom'])) {
            $qb
                ->andWhere('bank_operation.operationDate >= :dateFrom')
                ->setParameter('dateFrom', $searchValues['dateFrom']);
        }
        if (!empty($searchValues['dateTo'])) {
            $qb
                ->andWhere('bank_operation.operationDate <= :dateTo')
                ->setParameter('dateTo', $searchValues['dateTo']);
        }
        $qb->orderBy('bank_operation.operationDate', 'DESC');

        return $qb->getQuery();
    }
}
