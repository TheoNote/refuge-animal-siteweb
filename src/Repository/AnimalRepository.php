<?php

namespace App\Repository;

use App\Entity\Animal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AnimalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Animal::class);
    }

    public function save(?Animal $animal = null): void
    {
        $this->getEntityManager()->flush($animal);
    }

    public function persist(?Animal $animal = null): void
    {
        $this->getEntityManager()->persist($animal);
    }

    public function persistAndSave(?Animal $animal = null): void
    {
        $this->persist($animal);
        $this->save($animal);
    }

    public function delete(?Animal $animal = null): void
    {
        $this->getEntityManager()->remove($animal);
        $this->getEntityManager()->flush();
    }

    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
