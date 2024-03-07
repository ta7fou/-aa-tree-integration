<?php

namespace App\Entity;

use App\Repository\DashRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DashRepository::class)]
class Dash
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $campagne_id = null;

    #[ORM\Column]
    private ?int $nb_visites = null;

    #[ORM\Column]
    private ?int $nb_participations = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCampagneId(): ?int
    {
        return $this->campagne_id;
    }

    public function setCampagneId(int $campagne_id): static
    {
        $this->campagne_id = $campagne_id;

        return $this;
    }

    public function getNbVisites(): ?int
    {
        return $this->nb_visites;
    }

    public function setNbVisites(int $nb_visites): static
    {
        $this->nb_visites = $nb_visites;

        return $this;
    }

    public function getNbParticipations(): ?int
    {
        return $this->nb_participations;
    }

    public function setNbParticipations(int $nb_participations): static
    {
        $this->nb_participations = $nb_participations;

        return $this;
    }
}