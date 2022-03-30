<?php

namespace App\Entity;

use App\Repository\DroneDataPerReservationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DroneDataPerReservationRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class DroneDataPerReservation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=5000)
     */
    private $results;

    /**
     * @ORM\Column(type="float")
     */
    private $chemical_quantity_per_ha;

    /**
     * @ORM\Column(type="float")
     */
    private $water_quantity;

    /**
     * @ORM\OneToOne(targetEntity=Reservation::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $reservation;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $is_deleted = 0;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $created_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResults(): ?string
    {
        return $this->results;
    }

    public function setResults(string $results): self
    {
        $this->results = $results;

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

    public function getWaterQuantity(): ?float
    {
        return $this->water_quantity;
    }

    public function setWaterQuantity(float $water_quantity): self
    {
        $this->water_quantity = $water_quantity;

        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(Reservation $reservation): self
    {
        $this->reservation = $reservation;

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

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function convertDates(): void
    {
        $this->created_at = new \DateTime('@'.strtotime('now'));
    }

    public function __toString(): string
    {
        return $this->reservation;
    }
}
