<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findInactiveForMonth(): array
    {
        $date = date('Y-m-d H:i:s', strtotime('last month'));

        $qb = $this->createQueryBuilder('user')
            ->where('
                user.lastActionAt < :date AND
                user.inactive IS NULL
            ')
            ->setParameter('date', $date);

        $query = $qb->getQuery();

        return $query->execute();
    }
}
