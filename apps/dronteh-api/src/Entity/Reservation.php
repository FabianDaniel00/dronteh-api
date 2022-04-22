<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @Uploadable
 */
class Reservation
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
    private $parcel_number;

    /**
     * @ORM\Column(type="json")
     */
    private $gps_coordinates = ["", ""];

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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $time;

    /**
     * @ORM\Column(type="smallint", options={"default": 1})
     */
    private $status = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $to_be_present = 1;

    /**
     * @ORM\Column(type="string", length=5000, nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity=Plant::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $plant;

    /**
     * @ORM\Column(type="date")
     */
    private $reservation_interval_start;

    /**
     * @ORM\Column(type="date")
     */
    private $reservation_interval_end;

    /**
     * @ORM\Column(type="string", length=5000, nullable=true)
     */
    private $results;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @UploadableField(mapping="reservation", fileNameProperty="image")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $chemical_quantity_per_ha;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $water_quantity;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $updated_at;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $is_deleted = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
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

    public function getGpsCoordinates(): array
    {
        return $this->gps_coordinates;
    }

    public function setGpsCoordinates(array $gps_coordinates): self
    {
        $this->gps_coordinates = $gps_coordinates;

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

    public function setChemical(Chemical $chemical): self
    {
        $this->chemical = $chemical;

        return $this;
    }

    public function getTime(): ?\DateTime
    {
        return $this->time;
    }

    public function setTime(?\DateTime $time): self
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

    public function setPlant(Plant $plant): self
    {
        $this->plant = $plant;

        return $this;
    }

    public function getReservationIntervalStart(): ?\DateTime
    {
        return $this->reservation_interval_start;
    }

    public function setReservationIntervalStart(\DateTime $reservation_interval_start): self
    {
        $this->reservation_interval_start = $reservation_interval_start;

        return $this;
    }

    public function getReservationIntervalEnd(): ?\DateTime
    {
        return $this->reservation_interval_end;
    }

    public function setReservationIntervalEnd(\DateTime $reservation_interval_end): self
    {
        $this->reservation_interval_end = $reservation_interval_end;

        return $this;
    }

    public function getResults(): ?string
    {
        return $this->results;
    }

    public function setResults(?string $results): self
    {
        $this->results = $results;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
    }

    public function getChemicalQuantityPerHa(): ?float
    {
        return $this->chemical_quantity_per_ha;
    }

    public function setChemicalQuantityPerHa(?float $chemical_quantity_per_ha): self
    {
        $this->chemical_quantity_per_ha = $chemical_quantity_per_ha;

        return $this;
    }

    public function getWaterQuantity(): ?float
    {
        return $this->water_quantity;
    }

    public function setWaterQuantity(?float $water_quantity): self
    {
        $this->water_quantity = $water_quantity;

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

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTime $updated_at): self
    {
        $this->updated_at = $updated_at;

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

    /**
     * @ORM\PrePersist
     */
    public function convertDates(): void
    {
        $this->created_at = new \DateTime('@'.strtotime('now'));
        $this->updated_at = new \DateTime('@'.strtotime('now'));
    }

    /**
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->updated_at = new \DateTime('@'.strtotime('now'));
    }

    public function __toString(): string
    {
        return '#'.$this->parcel_number.' - '.$this->user->getFirstname().' '.$this->user->getLastname();
    }
}
