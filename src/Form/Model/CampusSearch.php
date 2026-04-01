<?php

namespace App\Form\Model;


use Symfony\Component\Validator\Constraints as Assert;

class CampusSearch
{

    #[Assert\NotBlank(message: 'Veuillez renseigner un nom pour le campus recherché')]
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
