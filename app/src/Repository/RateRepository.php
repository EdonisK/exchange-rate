<?php

namespace App\Repository;

use App\Entity\Rate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

/**
 * @method Rate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rate[]    findAll()
 * @method Rate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rate::class);
    }

    public function getRateFromLastHour(string $fromCurrency, $toCurrency)
    {
        $date = new DateTime();
        $date->modify('-1 hour');

        return $this
            ->createQueryBuilder('r')
            ->select('r.rate')
            ->andWhere('r.createdAt > :date')
            ->andWhere('r.fromCurrency = :fromCurrency')
            ->andWhere('r.toCurrency = :toCurrency')
            ->setParameters([
                'date' => $date,
                'fromCurrency' => $fromCurrency,
                'toCurrency' => $toCurrency
            ])
            ->getQuery()
            ->getResult();
    }
}
