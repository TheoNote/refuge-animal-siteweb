<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository  extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(?User $user = null): void
    {
        $this->getEntityManager()->flush($user);
    }

    public function persist(?User $user = null): void
    {
        $this->getEntityManager()->persist($user);
    }

    public function persistAndSave(?User $user = null): void
    {
        $this->persist($user);
        $this->save($user);
    }

    public function delete(?User $user = null): void
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }

    public function findUserByEmail(string $email): ?User
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $email);

        return $query->getQuery()->getOneOrNullResult();
    }
}
