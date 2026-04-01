<?php

namespace App\Form\Model;
use Symfony\Component\Validator\Constraints as Assert;

class CancelSortie
{
    #[Assert\Length(min:10,minMessage: 'Remplir avec au moins 10 caractères')]
  private string $descriptionCancel;

    public function getDescriptionCancel(): string
    {
        return $this->descriptionCancel;
    }

    public function setDescriptionCancel(string $descriptionCancel): void
    {
        $this->descriptionCancel = $descriptionCancel;
    }
}
