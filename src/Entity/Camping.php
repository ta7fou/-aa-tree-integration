<?php

namespace App\Entity;

use App\Repository\CampingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampingRepository::class)]
class Camping
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $prixcamp = null;

    #[ORM\Column(length: 255)]
    private ?string $adressecamp = null;

    #[ORM\Column]
    private ?int $nbpmax = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datedebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datefin = null;

    #[ORM\Column(length: 255)]
    private ?string $imagecamp = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Objectif $objid = null;
    

    public function getObjid()
    {
        return $this->objid;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrixcamp(): ?int
    {
        return $this->prixcamp;
    }

    public function setPrixcamp(int $prixcamp): static
    {
        $this->prixcamp = $prixcamp;

        return $this;
    }

    public function getAdressecamp(): ?string
    {
        return $this->adressecamp;
    }

    public function setAdressecamp(string $adressecamp): static
    {
        $this->adressecamp = $adressecamp;

        return $this;
    }

    public function getNbpmax(): ?int
    {
        return $this->nbpmax;
    }

    public function setNbpmax(int $nbpmax): static
    {
        $this->nbpmax = $nbpmax;

        return $this;
    }

    public function getDatedebut(): ?\DateTimeInterface
    {
        return $this->datedebut;
    }

    public function setDatedebut(\DateTimeInterface $datedebut): static
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(\DateTimeInterface $datefin): static
    {
        $this->datefin = $datefin;

        return $this;
    }

    public function getObjectif(): ?Objectif
    {
        return $this->objid;
    }

    public function setObjid(?Objectif $objid): static
    {
        $this->objid = $objid;

        return $this;
    }

    public function getImagecamp(): ?string
    {
        return $this->imagecamp;
    }

    public function setImagecamp(string $imagecamp): static
    {
        $this->imagecamp = $imagecamp;

        return $this;
    }

   
}
