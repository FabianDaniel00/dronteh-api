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
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $area_of_use;

    /**
     * @ORM\Column(type="float")
     */
    private $quantity_per_ha;

    /**
     * @ORM\Column(type="float")
     */
    private $price_per_liter;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAreaOfUse(): ?string
    {
        return $this->area_of_use;
    }

    public function setAreaOfUse(string $area_of_use): self
    {
        $this->area_of_use = $area_of_use;

        return $this;
    }

    public function getQuantityPerHa(): ?float
    {
        return $this->quantity_per_ha;
    }

    public function setQuantityPerHa(float $quantity_per_ha): self
    {
        $this->quantity_per_ha = $quantity_per_ha;

        return $this;
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
}