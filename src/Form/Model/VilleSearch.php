<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class VilleSearch
{

    #[Assert\NotBlank(message: 'Veuillez renseigner un nom pour la ville recherchée')]
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }
}
