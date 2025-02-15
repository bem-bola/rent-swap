<?php

namespace App\Entity;

use App\Repository\WarnUserRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WarnUserRepository::class)]
class WarnUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $isBanned = null;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $created;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $started = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $ended = null;

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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return bool|null
     */
    public function getIsBanned(): ?bool
    {
        return $this->isBanned;
    }

    /**
     * @param bool|null $isBanned
     */
    public function setIsBanned(?bool $isBanned): void
    {
        $this->isBanned = $isBanned;
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
    public function getStarted(): ?DateTimeInterface
    {
        return $this->started;
    }

    /**
     * @param DateTimeInterface|null $started
     */
    public function setStarted(?DateTimeInterface $started): void
    {
        $this->started = $started;
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

    public function isBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setBanned(?bool $isBanned): static
    {
        $this->isBanned = $isBanned;

        return $this;
    }

}
