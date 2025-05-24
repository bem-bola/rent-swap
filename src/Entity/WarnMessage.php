<?php

namespace App\Entity;

use App\Repository\WarnMessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WarnMessageRepository::class)]
class WarnMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['warn:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'warnMessages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['warn:read'])]
    private ?User $informant = null;

    #[ORM\ManyToOne(inversedBy: 'warnMessages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['warn:read'])]
    private ?Message $message = null;

    #[ORM\ManyToOne(inversedBy: 'reviewerWarnMessage')]
    #[Groups(['warn:read'])]
    private ?User $reviewer = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['warn:read'])]
    private ?\DateTime $created = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['warn:read'])]
    private ?\DateTime $reviewed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInformant(): ?User
    {
        return $this->informant;
    }

    public function setInformant(?User $informant): static
    {
        $this->informant = $informant;

        return $this;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getReviewer(): ?User
    {
        return $this->reviewer;
    }

    public function setReviewer(?User $reviewer): static
    {
        $this->reviewer = $reviewer;

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

    public function getReviewed(): ?\DateTime
    {
        return $this->reviewed;
    }

    public function setReviewed(?\DateTime $reviewed): static
    {
        $this->reviewed = $reviewed;

        return $this;
    }
}
