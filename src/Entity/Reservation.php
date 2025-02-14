<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: ReservationStatus::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ReservationStatus $status = null;

    #[ORM\ManyToOne(targetEntity: Device::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Device $device = null;

    #[ORM\Column(type: 'float')]
    private float $price;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $created;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $ended = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return ReservationStatus|null
     */
    public function getStatus(): ?ReservationStatus
    {
        return $this->status;
    }

    /**
     * @param ReservationStatus|null $status
     */
    public function setStatus(?ReservationStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * @return Device|null
     */
    public function getDevice(): ?Device
    {
        return $this->device;
    }

    /**
     * @param Device|null $device
     */
    public function setDevice(?Device $device): void
    {
        $this->device = $device;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCreated(): DateTimeInterface
    {
        return $this->created;
    }

    /**
     * @param DateTimeInterface $created
     */
    public function setCreated(DateTimeInterface $created): void
    {
        $this->created = $created;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getEnded(): ?DateTimeInterface
    {
        return $this->ended;
    }

    /**
     * @param DateTimeInterface|null $ended
     */
    public function setEnded(?DateTimeInterface $ended): void
    {
        $this->ended = $ended;
    }


}
