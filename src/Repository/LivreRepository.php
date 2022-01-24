<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Livre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livre[]    findAll()
 * @method Livre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    public function findByCriteria(array $criteria)
    {
        $qb = $this->createQueryBuilder('l');
        if ($criteria['titre'] !== null) {
            $qb->andWhere('l.titre LIKE :titre')
                ->setParameter('titre', '%'.$criteria['titre'].'%');
        }
        if ($criteria['from'] !== null) {
            $qb->andWhere('l.date_de_parution > :dateFrom')
                ->setParameter('dateFrom', $criteria['from']);
        }
        if ($criteria['to'] !== null) {
            $qb->andWhere('l.date_de_parution < :dateTo')
                ->setParameter('dateTo', $criteria['to']);
        }

        return $qb->orderBy('l.date_de_parution', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
