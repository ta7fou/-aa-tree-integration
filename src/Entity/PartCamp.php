<?php

namespace App\Entity;

use App\Repository\PartCampRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartCampRepository::class)]
class PartCamp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column]
    private ?int $user_id = null;
    
    

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datenaissance = null;

    #[ORM\Column(length: 255)]
    private ?string $nomuser = null;

    #[ORM\Column(length: 255)]
    private ?string $prenomuser = null;

    #[ORM\Column(length: 255)]
    private ?string $emailuser = null;

    #[ORM\ManyToOne(inversedBy: 'partCamps')]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $camping_id = null;

    
    public function getUserId(): ?int
    {
        return $this->user_id;
    }
    public function setUserId(?int $userId): static
    {
        $this->user_id = $userId;

        return $this;
    }
        
    public function getId(): ?int
    {
        return $this->id;
    }

    

    public function getDatenaissance(): ?\DateTimeInterface
    {
        return $this->datenaissance;
    }

    public function setDatenaissance(\DateTimeInterface $datenaissance): static
    {
        $this->datenaissance = $datenaissance;

        return $this;
    }

    public function getNomuser(): ?string
    {
        return $this->nomuser;
    }

    public function setNomuser(string $nomuser): static
    {
        $this->nomuser = $nomuser;

        return $this;
    }

    public function getPrenomuser(): ?string
    {
        return $this->prenomuser;
    }

    public function setPrenomuser(string $prenomuser): static
    {
        $this->prenomuser = $prenomuser;

        return $this;
    }

    public function getEmailuser(): ?string
    {
        return $this->emailuser;
    }

    public function setEmailuser(string $emailuser): static
    {
        $this->emailuser = $emailuser;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCampingId(): ?int
    {
        return $this->camping_id;
    }

    public function setCampingId(int $camping_id): static
    {
        $this->camping_id = $camping_id;

        return $this;
    }


}
