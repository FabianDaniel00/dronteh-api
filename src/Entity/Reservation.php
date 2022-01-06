<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */
class Reservation
{
    function __construct()
    {
        $this->created_at = new \DateTime('@'.strtotime('now'));
    }

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
    private $parcel_number;

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
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $is_deleted = 0;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $time;

    /**
     * @ORM\Column(type="smallint", options={"default": 0})
     */
    private $status;

    /**
     * @ORM\Column(type="boolean")
     */
    private $to_be_present;

    /**
     * @ORM\Column(type="string", length=5000, nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity=Plant::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $plant;

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

    public function getParcelNumber(): ?string
    {
        return $this->parcel_number;
    }

    public function setParcelNumber(string $parcel_number): self
    {
        $this->parcel_number = $parcel_number;

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

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;

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

    public function getTime(): ?\DateTime
    {
        return $this->time;
    }

    public function setTime(\DateTime $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getToBePresent(): ?bool
    {
        return $this->to_be_present;
    }

    public function setToBePresent(bool $to_be_present): self
    {
        $this->to_be_present = $to_be_present;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
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
}