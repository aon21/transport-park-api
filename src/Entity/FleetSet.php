<?php

namespace App\Entity;

use App\Repository\FleetSetRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: FleetSetRepository::class)]
#[ORM\Table(name: 'fleet_sets')]
#[ORM\HasLifecycleCallbacks]
class FleetSet
{
    public const STATUS_DOWNTIME = 'downtime';
    public const STATUS_WORKS = 'works';
    public const STATUS_FREE = 'free';
    
    private const VEHICLE_STATUS_IN_SERVICE = 'in_service';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id;

    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Truck::class)]
    #[ORM\JoinColumn(name: 'truck_id', referencedColumnName: 'id', nullable: false)]
    private Truck $truck;

    #[ORM\ManyToOne(targetEntity: Trailer::class)]
    #[ORM\JoinColumn(name: 'trailer_id', referencedColumnName: 'id', nullable: false)]
    private Trailer $trailer;

    /**
     * @var Collection<int, Driver>
     */
    #[ORM\OneToMany(targetEntity: Driver::class, mappedBy: 'fleetSet')]
    private Collection $drivers;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->drivers = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTruck(): Truck
    {
        return $this->truck;
    }

    public function setTruck(Truck $truck): static
    {
        $this->truck = $truck;

        return $this;
    }

    public function getTrailer(): Trailer
    {
        return $this->trailer;
    }

    public function setTrailer(Trailer $trailer): static
    {
        $this->trailer = $trailer;

        return $this;
    }

    /**
     * @return Collection<int, Driver>
     */
    public function getDrivers(): Collection
    {
        return $this->drivers;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getStatus(): string
    {
        if ($this->isInDowntime()) {
            return self::STATUS_DOWNTIME;
        }

        if ($this->hasDrivers()) {
            return self::STATUS_WORKS;
        }

        return self::STATUS_FREE;
    }

    private function isInDowntime(): bool
    {
        return $this->truck->getStatus() === self::VEHICLE_STATUS_IN_SERVICE
            || $this->trailer->getStatus() === self::VEHICLE_STATUS_IN_SERVICE;
    }

    private function hasDrivers(): bool
    {
        return $this->drivers->count() > 0;
    }
}

