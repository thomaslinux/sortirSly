<?php

namespace App\Form\Model;

use App\Entity\Campus;

class SortieSearch
{
    // TODO ajouter la validation des champs

    private ?string $nom = null;
    private ?Campus $campus = null;
    private ?\DateTime $dateHeureDebut = null;
    private ?\DateTime $dateHeureFin = null;

    private ?bool $organisateur = null;
    private ?bool $inscrit = null;
    private ?bool $pasInscrit = null;
    private ?bool $sortiesPassees = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }

    public function getDateHeureDebut(): ?\DateTime
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(?\DateTime $dateHeureDebut): void
    {
        $this->dateHeureDebut = $dateHeureDebut;
    }

    public function getDateHeureFin(): ?\DateTime
    {
        return $this->dateHeureFin;
    }

    public function setDateHeureFin(?\DateTime $dateHeureFin): void
    {
        $this->dateHeureFin = $dateHeureFin;
    }

    public function getOrganisateur(): ?bool
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?bool $organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    public function getInscrit(): ?bool
    {
        return $this->inscrit;
    }

    public function setInscrit(?bool $inscrit): void
    {
        $this->inscrit = $inscrit;
    }

    public function getPasInscrit(): ?bool
    {
        return $this->pasInscrit;
    }

    public function setPasInscrit(?bool $pasInscrit): void
    {
        $this->pasInscrit = $pasInscrit;
    }

    public function getSortiesPassees(): ?bool
    {
        return $this->sortiesPassees;
    }

    public function setSortiesPassees(?bool $sortiesPassees): void
    {
        $this->sortiesPassees = $sortiesPassees;
    }


}
