<?php

namespace App\Entity;

use App\Repository\OplatyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OplatyRepository::class)
 */
class Oplaty
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rodzaj;

    /**
     * @ORM\Column(type="integer")
     */
    private $cena;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="oplaty")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Jeziora::class, inversedBy="oplaty")
     */
    private $jezioro;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRodzaj(): ?string
    {
        return $this->rodzaj;
    }

    public function setRodzaj(string $rodzaj): self
    {
        $this->rodzaj = $rodzaj;

        return $this;
    }

    public function getCena(): ?int
    {
        return $this->cena;
    }

    public function setCena(int $cena): self
    {
        $this->cena = $cena;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getJezioro(): ?Jeziora
    {
        return $this->jezioro;
    }

    public function setJezioro(?Jeziora $jezioro): self
    {
        $this->jezioro = $jezioro;

        return $this;
    }
}
