<?php

namespace App\Form\Model;

class VilleSearch
{
// TODO ajouter la validation des champs

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
