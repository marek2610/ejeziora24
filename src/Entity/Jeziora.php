<?php

namespace App\Entity;

use App\Repository\JezioraRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo; 

/**
 * @ORM\Entity(repositoryClass=JezioraRepository::class)
 */
class Jeziora
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
    private $nazwa;

    /**
     * @Gedmo\Slug(fields={"nazwa"})
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2, nullable=true)
     */
    private $powierzchnia;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $pomosty;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $lodz;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $kusza;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fish;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $opis;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $utworzono;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private $aktywny = false;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="jeziora")
     * @ORM\JoinColumn(nullable=false)
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class, inversedBy="jeziora")
     * @ORM\JoinColumn(nullable=false)
     */
    private $region;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $modyfikacja;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $miejscowosc;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $brochureFilename;

    /**
     * @ORM\OneToMany(targetEntity=Oplaty::class, mappedBy="jezioro")
     */
    private $oplaty;

    public function __construct()
    {
        $this->oplaty = new ArrayCollection();
    }

    public function getBrochureFilename()
    {
        return $this->brochureFilename;
    }

    public function setBrochureFilename($brochureFilename)
    {
        $this->brochureFilename = $brochureFilename;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNazwa(): ?string
    {
        return $this->nazwa;
    }

    public function setNazwa(string $nazwa): self
    {
        $this->nazwa = $nazwa;

        return $this;
    }

    public function __toString() {
        return $this->nazwa;
    }

    public function getPowierzchnia(): ?string
    {
        return $this->powierzchnia;
    }

    public function setPowierzchnia(?string $powierzchnia): self
    {
        $this->powierzchnia = $powierzchnia;

        return $this;
    }

    public function getPomosty(): ?bool
    {
        return $this->pomosty;
    }

    public function setPomosty(?bool $pomosty): self
    {
        $this->pomosty = $pomosty;

        return $this;
    }

    public function getLodz(): ?bool
    {
        return $this->lodz;
    }

    public function setLodz(?bool $lodz): self
    {
        $this->lodz = $lodz;

        return $this;
    }

    public function getKusza(): ?bool
    {
        return $this->kusza;
    }

    public function setKusza(?bool $kusza): self
    {
        $this->kusza = $kusza;

        return $this;
    }

    public function getFish(): ?string
    {
        return $this->fish;
    }

    public function setFish(?string $fish): self
    {
        $this->fish = $fish;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getUtworzono(): ?\DateTimeInterface
    {
        return $this->utworzono;
    }

    public function setUtworzono(\DateTimeInterface $utworzono): self
    {
        $this->utworzono = $utworzono;

        return $this;
    }

    public function getAktywny(): ?bool
    {
        return $this->aktywny;
    }

    public function setAktywny(bool $aktywny): self
    {
        $this->aktywny = $aktywny;

        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getModyfikacja(): ?\DateTimeInterface
    {
        return $this->modyfikacja;
    }

    public function setModyfikacja(\DateTimeInterface $modyfikacja): self
    {
        $this->modyfikacja = $modyfikacja;

        return $this;
    }

    public function getMiejscowosc(): ?string
    {
        return $this->miejscowosc;
    }

    public function setMiejscowosc(?string $miejscowosc): self
    {
        $this->miejscowosc = $miejscowosc;

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
            $oplaty->setJezioro($this);
        }

        return $this;
    }

    public function removeOplaty(Oplaty $oplaty): self
    {
        if ($this->oplaty->contains($oplaty)) {
            $this->oplaty->removeElement($oplaty);
            // set the owning side to null (unless already changed)
            if ($oplaty->getJezioro() === $this) {
                $oplaty->setJezioro(null);
            }
        }

        return $this;
    }
}
