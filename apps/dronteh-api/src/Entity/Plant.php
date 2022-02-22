<?php

namespace App\Entity;

use App\Repository\PlantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlantRepository::class)
 */
class Plant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

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

    public function __construct()
    {
        $this->areaOfUseChemicals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setNameEn(?string $name_en): self
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
