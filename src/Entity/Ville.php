<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('villes-api')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('villes-api')]
    #[Assert\NotBlank(message: 'Veuillez renseigner un nom pour la ville')]
    #[Assert\Length(min: 3, minMessage: 'Remplir avec au moins 3 caractères')]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Veuillez renseigner un code postal pour la sortie')]
    #[Assert\Positive(message: 'Le code doit être positif (Pas de -)')]
    #[Assert\LessThanOrEqual(99999, message: 'Le code doit être valide')]
    #[Groups('villes-api')]
    private ?int $codePostal = null;

    /**
     * @var Collection<int, Lieu>
     */
    #[ORM\OneToMany(targetEntity: Lieu::class, mappedBy: 'ville',cascade: ['remove'])]
    #[Groups('villes-api')]
    private Collection $lieux;

    public function __construct()
    {
        $this->lieux = new ArrayCollection();
    }

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

    public function getCodePostal(): ?int
    {
        return $this->codePostal;
    }

    public function setCodePostal(int $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * @return Collection<int, Lieu>
     */
    public function getLieux(): Collection
    {
        return $this->lieux;
    }

    public function addLieux(Lieu $lieux): static
    {
        if (!$this->lieux->contains($lieux)) {
            $this->lieux->add($lieux);
            $lieux->setVille($this);
        }

        return $this;
    }

    public function removeLieux(Lieu $lieux): static
    {
        if ($this->lieux->removeElement($lieux)) {
            // set the owning side to null (unless already changed)
            if ($lieux->getVille() === $this) {
                $lieux->setVille(null);
            }
        }

        return $this;
    }
}
