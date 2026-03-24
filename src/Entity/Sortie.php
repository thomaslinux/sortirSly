<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un nom pour la sortie')]
    #[Assert\Length(min:3,minMessage: 'Remplir avec au moins 3 caractères')]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]

    #[Assert\GreaterThan('now', message:'Choisir une date/heure à partir de maintenant')]
    private ?\DateTime $dateHeureDebut = null;

    #[ORM\Column(nullable: true)]

    #[Assert\LessThanOrEqual( propertyPath: 'dateHeureDebut', message:'Choisir une date AVANT la date de début de la sortie')]
    private ?\DateTime $dateLimiteInscription = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Veuillez renseigner une durée')]
    #[Assert\Positive]
    private ?int $duree = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(min:10,minMessage: 'Remplir avec au moins 10 caractères')]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\Positive]
    #[Assert\GreaterThanOrEqual(2)]
    private ?int $nbPlaces = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateHeureDebut(): ?\DateTime
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(?\DateTime $dateHeureDebut): static
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTime
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(?\DateTime $dateLimiteInscription): static
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getNbPlaces(): ?int
    {
        return $this->nbPlaces;
    }

    public function setNbPlaces(int $nbPlaces): static
    {
        $this->nbPlaces = $nbPlaces;

        return $this;
    }
}
