<?php

namespace App\Entity;

use App\Repository\ChemicalRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChemicalRepository::class)
 */
class Chemical
{
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name_hu;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_en;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name_sr;

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

    public function getNameSr(): ?string
    {
        return $this->name_sr;
    }

    public function setNameSr(?string $name_sr): self
    {
        $this->name_sr = $name_sr;

        return $this;
    }
}