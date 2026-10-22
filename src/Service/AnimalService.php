<?php

namespace App\Service;

use App\Entity\Animal;
use App\Enum\AnimalStatus;
use App\Repository\AnimalRepository;

class AnimalService
{
    public function __construct(
        private AnimalRepository $animalRepository,
    ) {
    }

    public function create(Animal $animal): void
    {
        if ($animal->getStatus() === null) {
            $animal->setStatus(AnimalStatus::Available);
        }

        $this->animalRepository->persistAndSave($animal);
    }

    public function update(Animal $animal): void
    {
        $this->animalRepository->persistAndSave($animal);
    }

    public function delete(Animal $animal): void
    {
        $this->animalRepository->delete($animal);
    }
}
