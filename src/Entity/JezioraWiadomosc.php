<?php

namespace App\Entity;

use App\Repository\JezioraWiadomoscRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JezioraWiadomoscRepository::class)
 */
class JezioraWiadomosc
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $imie;

    /**
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    private $nazwisko;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private $telefon;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $wiadomosc;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImie(): ?string
    {
        return $this->imie;
    }

    public function setImie(?string $imie): self
    {
        $this->imie = $imie;

        return $this;
    }

    public function getNazwisko(): ?string
    {
        return $this->nazwisko;
    }

    public function setNazwisko(?string $nazwisko): self
    {
        $this->nazwisko = $nazwisko;

        return $this;
    }

    public function getTelefon(): ?string
    {
        return $this->telefon;
    }

    public function setTelefon(?string $telefon): self
    {
        $this->telefon = $telefon;

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

    public function getWiadomosc(): ?string
    {
        return $this->wiadomosc;
    }

    public function setWiadomosc(string $wiadomosc): self
    {
        $this->wiadomosc = $wiadomosc;

        return $this;
    }
}
