<?php

namespace App\Entity;

use App\Repository\EmailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmailRepository::class)]
class Email
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'emails')]
    #[Assert\NotBlank()]
    private ?User $sender = null;

    #[ORM\ManyToOne]
    #[Assert\NotBlank()]
    private ?User $receiver = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank()]
    private ?string $Content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adressSender = null;

    #[ORM\ManyToOne]
    private ?Device $device = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $object = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $created = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->Content;
    }

    public function setContent(string $Content): static
    {
        $this->Content = $Content;

        return $this;
    }

    public function getAdressSender(): ?string
    {
        return $this->adressSender;
    }

    public function setAdressSender(?string $adressSender): static
    {
        $this->adressSender = $adressSender;

        return $this;
    }

    public function getDevice(): ?Device
    {
        return $this->device;
    }

    public function setDevice(?Device $device): static
    {
        $this->device = $device;

        return $this;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(?string $object): static
    {
        $this->object = $object;

        return $this;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function setCreated(?\DateTime $created): static
    {
        $this->created = $created;

        return $this;
    }
}
