<?php

namespace App\Entity;

use App\Repository\ChemicalsOfLandRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChemicalsOfLandRepository::class)
 */
class ChemicalsOfLand
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=LandPerClient::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $land;

    /**
     * @ORM\ManyToOne(targetEntity=Chemical::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $chemical;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLand(): ?LandPerClient
    {
        return $this->land;
    }

    public function setLand(?LandPerClient $land): self
    {
        $this->land = $land;

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