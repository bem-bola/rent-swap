<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Service\Constances;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    #[Groups(['device:read', 'user:read', 'warn:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['device:read', 'user:read', 'warn:read'])]
    #[Assert\NotBlank(message: 'L\'email est requis.')]
    #[Assert\Length(min: 2, minMessage: 'L\'email doit contenir au moins {{ limit }} caractères.')]
    #[Assert\Email(message: 'L\'adresse email "{{ value }}" n\'est pas valide.')]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['device:read', 'user:read'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!#%*\-?&])[A-Za-z\d@$!#%*\-?&]{12,}$/',
        message: 'Le mot de passe doit contenir au moins 12 caractères, avec au moins une majuscule, une minuscule, un chiffre et un caractère spécial.'
    )]
    private ?string $password = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['device:read', 'user:read', 'warn:read'])]
    private DateTimeInterface $created;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups(['device:read', 'user:read', 'warn:read'])]
    private bool $isVerified;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups(['device:read', 'user:read', 'warn:read'])]
    private ?bool $isDeleted = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['device:read', 'user:read', 'warn:read'])]
    #[Assert\NotBlank()]
    #[Assert\Regex(
        pattern: '/^[A-Za-zÀ-ÖØ-öø-ÿ]{2,}$/',
        message: 'Le nom doit contenir au moins 2 caractères alphabétiques.'
    )]
    private string $username;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['device:read', 'user:read', 'warn:read'])]
    #[Assert\NotBlank()]
    #[Assert\Regex(
        pattern: '/^[A-Za-zÀ-ÖØ-öø-ÿ]{2,}$/',
        message: 'Le nom doit contenir au moins 2 caractères alphabétiques.'
    )]
    private ?string $firstname = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['device:read', 'user:read', 'warn:read'])]
    #[Assert\NotBlank()]
    #[Assert\Regex(
        pattern: '/^[A-Za-zÀ-ÖØ-öø-ÿ]{2,}$/',
        message: 'Le nom doit contenir au moins 2 caractères alphabétiques.'
    )]
    private ?string $lastname = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['device:read', 'user:read'])]
    private ?\DateTimeImmutable $birthAt = null;

    /**
     * @var Collection<int, Conversation>
     */
    #[ORM\ManyToMany(targetEntity: Conversation::class, mappedBy: 'users')]
    private Collection $conversations;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'author')]
    private Collection $messages;

    #[ORM\ManyToOne]
    #[Groups(['device:read', 'user:read', 'warn:read'])]
    private ?Media $avatar = null;

    /**
     * @var Collection<int, Email>
     */
    #[ORM\OneToMany(targetEntity: Email::class, mappedBy: 'sender')]
    private Collection $emails;

    #[ORM\Column(nullable: true)]
    #[Groups(['device:read', 'user:read', 'warn:read'])]
    private ?\DateTime $deleted = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['device:read', 'user:read'])]
    private ?\DateTime $banned = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['device:read', 'user:read'])]
    private ?bool $isBanned = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['device:read', 'user:read'])]
    private ?\DateTime $verified = null;

    #[ORM\Column(nullable: true)]
    private ?array $oldPasswords = [];

    /**
     * @var Collection<int, WarnMessage>
     */
    #[ORM\OneToMany(targetEntity: WarnMessage::class, mappedBy: 'informant')]
    private Collection $warnMessages;

    #[ORM\Column(nullable: true)]
    private ?bool $isSuspended = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $suspended = null;

    /**
     * @var Collection<int, WarnMessage>
     */
    #[ORM\OneToMany(targetEntity: WarnMessage::class, mappedBy: 'reviewer')]
    private Collection $reviewerWarnMessage;

    public function __construct()
    {
        $this->conversations = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->emails = new ArrayCollection();
        $this->warnMessages = new ArrayCollection();
        $this->reviewerWarnMessage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        // Initialisation du tableau si nécessaire
        if ($this->oldPasswords === null) {
            $this->oldPasswords = [];
        }

        // Si un mot de passe actuel existe ET qu'il est différent du nouveau
        if (!empty($this->password) && $this->password !== $password) {
            // Ajout de l'ancien mot de passe en tête
            array_unshift($this->oldPasswords, $this->password);

            // Limiter à N anciens mots de passe (défini dans la constante)
            $this->oldPasswords = array_slice($this->oldPasswords, 0, Constances::NB_REPET_PASSWORD);
        }

        // Mise à jour du mot de passe
        $this->password = $password;

        return $this;
    }


    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @param DateTimeInterface $created
     */
    public function setCreated(DateTimeInterface $created): void
    {
        $this->created = $created;
    }

    public function getCreated(): ?DateTimeInterface
    {
        return $this->created;
    }

    /**
     * @return string|null
     */
    public function getSiret(): ?string
    {
        return $this->siret;
    }

    /**
     * @param string|null $siret
     */
    public function setSiret(?string $siret): void
    {
        $this->siret = $siret;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * @param bool $isVerified
     */
    public function setIsVerified(bool $isVerified): void
    {
        $this->isVerified = $isVerified;
    }

    /**
     * @return bool|null
     */
    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool|null $isDeleted
     */
    public function setIsDeleted(?bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     */
    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     */
    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function setVerified(\DateTime $date): static
    {
        $this->verified = $date;

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setDeleted(?\DateTime $deleted): static
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getBirthAt(): ?\DateTimeImmutable
    {
        return $this->birthAt;
    }

    public function setBirthAt(?\DateTimeImmutable $birthAt): static
    {
        $this->birthAt = $birthAt;

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): static
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations->add($conversation);
            $conversation->addUser($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): static
    {
        if ($this->conversations->removeElement($conversation)) {
            $conversation->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setAuthor($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getAuthor() === $this) {
                $message->setAuthor(null);
            }
        }

        return $this;
    }

    public function getAvatar(): ?Media
    {
        return $this->avatar;
    }

    public function setAvatar(?Media $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, Email>
     */
    public function getEmails(): Collection
    {
        return $this->emails;
    }

    public function addEmail(Email $email): static
    {
        if (!$this->emails->contains($email)) {
            $this->emails->add($email);
            $email->setSender($this);
        }

        return $this;
    }

    public function removeEmail(Email $email): static
    {
        if ($this->emails->removeElement($email)) {
            // set the owning side to null (unless already changed)
            if ($email->getSender() === $this) {
                $email->setSender(null);
            }
        }

        return $this;
    }

    public function getDeleted(): ?\DateTime
    {
        return $this->deleted;
    }

    public function getBanned(): ?\DateTime
    {
        return $this->banned;
    }

    public function setBanned(?\DateTime $banned): static
    {
        $this->banned = $banned;

        return $this;
    }

    public function isBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(?bool $isBanned): static
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    public function getVerified(): ?\DateTime
    {
        return $this->verified;
    }

    public function getOldPasswords(): ?array
    {
        return $this->oldPasswords;
    }

    public function setOldPasswords(?array $oldPasswords): static
    {
        $this->oldPasswords = $oldPasswords;

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
            $warnMessage->setInformant($this);
        }

        return $this;
    }

    public function removeWarnMessage(WarnMessage $warnMessage): static
    {
        if ($this->warnMessages->removeElement($warnMessage)) {
            // set the owning side to null (unless already changed)
            if ($warnMessage->getInformant() === $this) {
                $warnMessage->setInformant(null);
            }
        }

        return $this;
    }

    public function isSuspended(): ?bool
    {
        return $this->isSuspended;
    }

    public function setIsSuspended(?bool $isSuspended): static
    {
        $this->isSuspended = $isSuspended;

        return $this;
    }

    public function getSuspended(): ?\DateTime
    {
        return $this->suspended;
    }

    public function setSuspended(?\DateTime $suspended): static
    {
        $this->suspended = $suspended;

        return $this;
    }

    /**
     * @return Collection<int, WarnMessage>
     */
    public function getReviewerWarnMessage(): Collection
    {
        return $this->reviewerWarnMessage;
    }

    public function addReviewerWarnMessage(WarnMessage $reviewerWarnMessage): static
    {
        if (!$this->reviewerWarnMessage->contains($reviewerWarnMessage)) {
            $this->reviewerWarnMessage->add($reviewerWarnMessage);
            $reviewerWarnMessage->setReviewer($this);
        }

        return $this;
    }

    public function removeReviewerWarnMessage(WarnMessage $reviewerWarnMessage): static
    {
        if ($this->reviewerWarnMessage->removeElement($reviewerWarnMessage)) {
            // set the owning side to null (unless already changed)
            if ($reviewerWarnMessage->getReviewer() === $this) {
                $reviewerWarnMessage->setReviewer(null);
            }
        }

        return $this;
    }
}
