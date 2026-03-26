<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class UserImport
{
    /** le dto permet la validation automatique (taille (5Mo), type (csv))
     *evite de mapper directement sur participant
     *symfony avec le csvFile transforme l'upload en objet file
     */
    #[Assert\File(maxSize: '5M', mimeTypes: ['text/csv', 'text/plain'],
    mimeTypesMessage: 'Fichier CSV requis.')]
    #[Assert\NotNull(message: 'Sélectionnez un fichier.')]
    public ?File $csvFile = null;
}
