<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'id_user')]
    private ?int $id_user = null;

    #[ORM\Column(length: 255)]
    private ?string $Sujet = null;

    #[ORM\Column(length: 255)]
    private ?string $details = null;

    #[ORM\ManyToOne(inversedBy: 'demandes')]
    #[ORM\JoinColumn(name: 'animal_id', referencedColumnName: 'id', nullable: false)]
    private ?Animals $animalId = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSujet(): ?string
    {
        return $this->Sujet;
    }

    public function setSujet(string $Sujet): self
    {
        $this->Sujet = $Sujet;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(string $details): self
    {
        $this->details = $details;

        return $this;
    }

    
    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(?int $idUser): self
    {
        $this->id_user = $idUser;

        return $this;
    }


    public function getAnimalId(): ?Animals
    {
        return $this->animalId;
    }

    public function setAnimalId(?Animals $animalId): self
    {
        $this->animalId = $animalId;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
