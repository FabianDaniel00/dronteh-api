<?php

namespace App\Entity;

use App\Repository\AreaOfUseChemicalRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=AreaOfUseChemicalRepository::class)
 * @UniqueEntity(
 *     fields={"plant", "chemical"},
 *     errorPath="chemical",
 *     message="api.area_of_use_chemicals.new.constraint.unique"
 * )
 */
class AreaOfUseChemical
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Plant::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $plant;

    /**
     * @ORM\ManyToOne(targetEntity=Chemical::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $chemical;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlant(): ?Plant
    {
        return $this->plant;
    }

    public function setPlant(?Plant $plant): self
    {
        $this->plant = $plant;

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
}
