<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Users implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Jeziora::class, mappedBy="users", orphanRemoval=true)
     */
    private $jeziora;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imie;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nazwisko;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nazwa;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $telefon;

    /**
     * @ORM\OneToMany(targetEntity=Oplaty::class, mappedBy="user")
     */
    private $oplaty;

    /**
     * @ORM\Column(type="string", length=26, nullable=true)
     */
    private $nr_konta;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $bank;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $www;

    public function __construct()
    {
        $this->jeziora = new ArrayCollection();
        $this->oplaty = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function __toString() {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    // public function getRoles(): array
    // {
    //     $roles = $this->roles;
    //     // guarantee every user at least has ROLE_USER
    //     $roles[] = 'ROLE_USER';

    //     return array_unique($roles);
    // }

    public function getRoles()
    {
        $roles = $this->roles;
        // give everyone ROLE_USER!
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        return $roles;
    }

    //public function setRoles(array $roles): self
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    /**
     * @return Collection|Jeziora[]
     */
    public function getJeziora(): Collection
    {
        return $this->jeziora;
    }

    public function addJeziora(Jeziora $jeziora): self
    {
        if (!$this->jeziora->contains($jeziora)) {
            $this->jeziora[] = $jeziora;
            $jeziora->setUsers($this);
        }

        return $this;
    }

    public function removeJeziora(Jeziora $jeziora): self
    {
        if ($this->jeziora->contains($jeziora)) {
            $this->jeziora->removeElement($jeziora);
            // set the owning side to null (unless already changed)
            if ($jeziora->getUsers() === $this) {
                $jeziora->setUsers(null);
            }
        }

        return $this;
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

    public function getNazwa(): ?string
    {
        return $this->nazwa;
    }

    public function setNazwa(?string $nazwa): self
    {
        $this->nazwa = $nazwa;

        return $this;
    }

    /**
     * @return Collection|Oplaty[]
     */
    public function getOplaty(): Collection
    {
        return $this->oplaty;
    }

    public function addOplaty(Oplaty $oplaty): self
    {
        if (!$this->oplaty->contains($oplaty)) {
            $this->oplaty[] = $oplaty;
            $oplaty->setUser($this);
        }

        return $this;
    }

    public function removeOplaty(Oplaty $oplaty): self
    {
        if ($this->oplaty->contains($oplaty)) {
            $this->oplaty->removeElement($oplaty);
            // set the owning side to null (unless already changed)
            if ($oplaty->getUser() === $this) {
                $oplaty->setUser(null);
            }
        }

        return $this;
    }

    public function getNrKonta(): ?string
    {
        return $this->nr_konta;
    }

    public function setNrKonta(?string $nr_konta): self
    {
        $this->nr_konta = $nr_konta;

        return $this;
    }

    public function getBank(): ?string
    {
        return $this->bank;
    }

    public function setBank(?string $bank): self
    {
        $this->bank = $bank;

        return $this;
    }

    public function getWww(): ?string
    {
        return $this->www;
    }

    public function setWww(?string $www): self
    {
        $this->www = $www;

        return $this;
    }
}
