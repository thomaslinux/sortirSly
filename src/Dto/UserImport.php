<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class UserImport
{
    #[Assert\File(maxSize: '5M', mimeTypes: ['text/csv', 'text/plain'],
    mimeTypesMessage: 'Fichier CSV requis.')]
    #[Assert\NotNull(message: 'Sélectionnez un fichier.')]
    public ?File $csvFile = null;
}
