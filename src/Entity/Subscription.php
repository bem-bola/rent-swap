<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Plan::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Plan $plan = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $stripeId;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $currentPeriodStart;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $currentPeriodEnd;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive;

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
     * @return Plan|null
     */
    public function getPlan(): ?Plan
    {
        return $this->plan;
    }

    /**
     * @param Plan|null $plan
     */
    public function setPlan(?Plan $plan): void
    {
        $this->plan = $plan;
    }

    /**
     * @return string
     */
    public function getStripeId(): string
    {
        return $this->stripeId;
    }

    /**
     * @param string $stripeId
     */
    public function setStripeId(string $stripeId): void
    {
        $this->stripeId = $stripeId;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCurrentPeriodStart(): DateTimeInterface
    {
        return $this->currentPeriodStart;
    }

    /**
     * @param DateTimeInterface $currentPeriodStart
     */
    public function setCurrentPeriodStart(DateTimeInterface $currentPeriodStart): void
    {
        $this->currentPeriodStart = $currentPeriodStart;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCurrentPeriodEnd(): DateTimeInterface
    {
        return $this->currentPeriodEnd;
    }

    /**
     * @param DateTimeInterface $currentPeriodEnd
     */
    public function setCurrentPeriodEnd(DateTimeInterface $currentPeriodEnd): void
    {
        $this->currentPeriodEnd = $currentPeriodEnd;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }



}
