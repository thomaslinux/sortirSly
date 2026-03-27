<?php

namespace App\Dto;

use App\Entity\Campus;
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
    private ?File $csvFile = null;

    #[Assert\NotNull(message: 'Sélectionnez un campus.')]
    private ?Campus $campus = null;

    #[Assert\NotBlank(message: 'Saisissez un mot de passe par défaut.')]
    #[Assert\Regex(
        pattern: '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).+$/',
        message: 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.'
    )]
    #[Assert\Length(
        min: 8,
        minMessage: 'Le mot de passe doit faire au moins {{ limit }} caractères.'
    )]
    private ?string $plainPassword = null;

    public function getCsvFile(): ?File
    {
        return $this->csvFile;
    }

    public function setCsvFile(?File $csvFile): void
    {
        $this->csvFile = $csvFile;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }
}
