<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    #[Groups(['warn:read'])]
    private int $id;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['warn:read'])]
    private string $content;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['warn:read'])]
    private ?User $author;

    #[ORM\Column]
    #[Groups(['warn:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private Conversation $conversation;

    #[ORM\Column(nullable: true)]
    #[Groups(['warn:read'])]
    private ?\DateTime $deletedAt = null;

    /**
     * @var Collection<int, WarnMessage>
     */
    #[ORM\OneToMany(targetEntity: WarnMessage::class, mappedBy: 'message')]
    private Collection $warnMessages;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->warnMessages = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): static
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return Collection<int, WarnMessage>
     */
    public function getWarnMessages(): Collection
    {
        return $this->warnMessages;
    }

    public function addWarnMessage(WarnMessage $warnMessage): static
    {
        if (!$this->warnMessages->contains($warnMessage)) {
            $this->warnMessages->add($warnMessage);
            $warnMessage->setMessage($this);
        }

        return $this;
    }

    public function removeWarnMessage(WarnMessage $warnMessage): static
    {
        if ($this->warnMessages->removeElement($warnMessage)) {
            // set the owning side to null (unless already changed)
            if ($warnMessage->getMessage() === $this) {
                $warnMessage->setMessage(null);
            }
        }

        return $this;
    }
}
