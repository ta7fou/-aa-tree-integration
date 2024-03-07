<?php

namespace App\Entity;

use App\Repository\ObjectifRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObjectifRepository::class)]
class Objectif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $descobj = null;

    #[ORM\Column(length: 255)]
    private ?string $resultatobj = null;

    #[ORM\Column(length: 255)]
    private ?string $imageobj = null;
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescobj(): ?string
    {
        return $this->descobj;
    }

    public function setDescobj(string $descobj): static
    {
        $this->descobj = $descobj;

        return $this;
    }

    public function getResultatobj(): ?string
    {
        return $this->resultatobj;
    }

    public function setResultatobj(string $resultatobj): static
    {
        $this->resultatobj = $resultatobj;

        return $this;
    }

    public function getImageobj(): ?string
    {
        return $this->imageobj;
    }

    public function setImageobj(string $imageobj): static
    {
        $this->imageobj = $imageobj;

        return $this;
    }

   
}
