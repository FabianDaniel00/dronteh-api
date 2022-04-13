<?php

namespace App\Entity;

use App\Entity\Rating;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Locale;
use App\Repository\ChemicalRepository;

/**
 * @ORM\Entity(repositoryClass=ChemicalRepository::class)
 */
class Chemical
{
    public function __construct()
    {
        $this->ratings = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $price_per_liter;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $is_deleted = 0;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_hu;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_en;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_sr_Latn;

    /**
     * @ORM\OneToMany(targetEntity=Rating::class, mappedBy="chemical", orphanRemoval=true)
     */
    private $ratings;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPricePerLiter(): ?float
    {
        return $this->price_per_liter;
    }

    public function setPricePerLiter(float $price_per_liter): self
    {
        $this->price_per_liter = $price_per_liter;

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->is_deleted;
    }

    public function setIsDeleted(bool $is_deleted): self
    {
        $this->is_deleted = $is_deleted;

        return $this;
    }

    public function getAreaOfUse(): ?array
    {
        return $this->area_of_use;
    }

    public function setAreaOfUse(array $area_of_use): self
    {
        $this->area_of_use = $area_of_use;

        return $this;
    }

    public function getNameHu(): ?string
    {
        return $this->name_hu;
    }

    public function setNameHu(?string $name_hu): self
    {
        $this->name_hu = $name_hu;

        return $this;
    }

    public function getNameEn(): ?string
    {
        return $this->name_en;
    }

    public function setNameEn(string $name_en): self
    {
        $this->name_en = $name_en;

        return $this;
    }

    public function getNameSrLatn(): ?string
    {
        return $this->name_sr_Latn;
    }

    public function setNameSrLatn(?string $name_sr_Latn): self
    {
        $this->name_sr_Latn = $name_sr_Latn;

        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function getAvgRating(): ?string
    {
        $sumRating = 0;
        $ratings = $this->ratings->getValues();

        foreach ($ratings as $rating) {
            $sumRating += $rating->getRating();
        }

        $ratingsCount = count($ratings);
        return $ratingsCount ? $sumRating / $ratingsCount.' | '.$ratingsCount : '0 | 0';
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
            $rating->setChemical($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getChemical() === $this) {
                $rating->setChemical(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->{'name_'.Locale::getDefault()};
    }
}
