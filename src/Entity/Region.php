<?php

namespace App\Entity;

use App\Repository\RegionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo; 

/**
 * @ORM\Entity(repositoryClass=RegionRepository::class)
 */
class Region
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=70)
     */
    private $wojewodztwo;

    /**
     * @ORM\OneToMany(targetEntity=Jeziora::class, mappedBy="region")
     */
    private $jeziora;

    /**
     * @Gedmo\Slug(fields={"wojewodztwo"})
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    public function __construct()
    {
        $this->jeziora = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWojewodztwo(): ?string
    {
        return $this->wojewodztwo;
    }

    public function setWojewodztwo(string $wojewodztwo): self
    {
        $this->wojewodztwo = $wojewodztwo;

        return $this;
    }

    public function __toString() {
        return $this->wojewodztwo;
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
            $jeziora->setRegion($this);
        }

        return $this;
    }

    public function removeJeziora(Jeziora $jeziora): self
    {
        if ($this->jeziora->contains($jeziora)) {
            $this->jeziora->removeElement($jeziora);
            // set the owning side to null (unless already changed)
            if ($jeziora->getRegion() === $this) {
                $jeziora->setRegion(null);
            }
        }

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
}
