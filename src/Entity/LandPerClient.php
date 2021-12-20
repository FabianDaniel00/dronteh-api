<?php

namespace App\Entity;

use App\Repository\LandPerClientRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LandPerClientRepository::class)
 */
class LandPerClient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $land_name;

    /**
     * @ORM\Column(type="float")
     */
    private $land_area;

    /**
     * @ORM\ManyToOne(targetEntity=Chemical::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $chemical;

    /**
     * @ORM\Column(type="float")
     */
    private $chemical_quantity_per_ha;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getLandName(): ?string
    {
        return $this->land_name;
    }

    public function setLandName(string $land_name): self
    {
        $this->land_name = $land_name;

        return $this;
    }

    public function getLandArea(): ?float
    {
        return $this->land_area;
    }

    public function setLandArea(float $land_area): self
    {
        $this->land_area = $land_area;

        return $this;
    }

    public function getChemical(): ?Chemical
    {
        return $this->chemical;
    }

    public function setChemical(?Chemical $chemical): self
    {
        $this->chemical = $chemical;

        return $this;
    }

    public function getChemicalQuantityPerHa(): ?float
    {
        return $this->chemical_quantity_per_ha;
    }

    public function setChemicalQuantityPerHa(float $chemical_quantity_per_ha): self
    {
        $this->chemical_quantity_per_ha = $chemical_quantity_per_ha;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}