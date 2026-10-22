<?php

namespace App\Entity;

use App\Enum\AnimalStatus;
use App\Repository\AnimalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
#[ORM\Table(name: 'animals')]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom de l'animal est obligatoire.")]
    #[Assert\Length(max: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "L'espèce est obligatoire.")]
    #[Assert\Length(max: 100)]
    private ?string $species = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "La race est obligatoire.")]
    #[Assert\Length(max: 100)]
    private ?string $breed = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull(message: "La date de naissance est obligatoire.")]
    private ?\DateTimeImmutable $birthdate = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    private ?string $description = null;

    #[ORM\Column(enumType: AnimalStatus::class)]
    #[Assert\NotNull(message: "Le statut est obligatoire.")]
    private ?AnimalStatus $status = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message: "L'emoji est obligatoire.")]
    #[Assert\Length(max: 10)]
    private ?string $emoji = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        if ($this->status === null) {
            $this->status = AnimalStatus::Available;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSpecies(): ?string
    {
        return $this->species;
    }

    public function setSpecies(string $species): static
    {
        $this->species = $species;

        return $this;
    }

    public function getBreed(): ?string
    {
        return $this->breed;
    }

    public function setBreed(string $breed): static
    {
        $this->breed = $breed;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeImmutable
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeImmutable $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?AnimalStatus
    {
        return $this->status;
    }

    public function setStatus(AnimalStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getEmoji(): ?string
    {
        return $this->emoji;
    }

    public function setEmoji(string $emoji): static
    {
        $this->emoji = $emoji;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
