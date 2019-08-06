<?php

namespace UserBundle\Repository;

use UserBundle\Entity\User;

/**
 * Class UserRepository
 * @package UserBundle\Repository
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function getUserById($userId)
    {
        $queryBuilder = $this->createQueryBuilder('user')
            ->andWhere('user.id  = :userId');
        $queryBuilder->setParameter('userId', $userId);

        $query = $queryBuilder->getQuery();


        $user = $query->getResult();

        return $user;
    }
}