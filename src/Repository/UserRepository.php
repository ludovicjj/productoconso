<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class UserRepository
 * @package App\Repository
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $token
     * @throws NonUniqueResultException
     * @return null|User
     */
    public function findUserByForgottenPasswordToken(string $token): ?User
    {
        return $this->createQueryBuilder("u")
            ->where("u.forgottenPassword.token = :token")
            ->setParameter("token", $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $email
     * @param string $excludedEmail
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function findUserByEmailExcludeCurrentUserEmail(string $email, string $excludedEmail): ?User
    {
        $qb =  $this->getEntityManager()->createQueryBuilder();

        return $qb->select('u')
            ->from('App\Entity\User', 'u')
            ->where(
                $qb->expr()->notLike("u.email", "?2"),
                $qb->expr()->like("u.email", "?1")
            )
            ->setParameter(1, $email)
            ->setParameter(2, $excludedEmail)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
