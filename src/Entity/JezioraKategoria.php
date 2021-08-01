<?php

namespace App\Entity;

use App\Repository\JezioraKategoriaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JezioraKategoriaRepository::class)
 */
class JezioraKategoria
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    private $kategoria;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $opis;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKategoria(): ?string
    {
        return $this->kategoria;
    }

    public function setKategoria(?string $kategoria): self
    {
        $this->kategoria = $kategoria;

        return $this;
    }

    public function getOpis(): ?string
    {
        return $this->opis;
    }

    public function setOpis(?string $opis): self
    {
        $this->opis = $opis;

        return $this;
    }
}
