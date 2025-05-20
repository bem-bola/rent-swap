<?php
namespace App\Entity;

use App\Repository\DeviceRepository;
use App\Service\Constances;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DeviceRepository::class)]
class Device
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    #[Groups(['device:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['device:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Device::class, cascade: ['persist'])]
    private ?Device $parent = null;

    #[ORM\ManyToOne(targetEntity: TypeDevice::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['device:read'])]
    private TypeDevice $type;

    #[ORM\Column(type: 'string', length: 250)]
    #[Groups(['device:read'])]
    private string $slug;

    #[ORM\Column(type: 'text')]
    #[Groups(['device:read'])]
    private string $body;

    #[ORM\Column(type: 'float')]
    #[Groups(['device:read'])]
    private float $price;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups(['device:read'])]
    private ?bool $showPhone = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['device:read'])]
    private \DateTimeInterface $created;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['device:read'])]
    private ?\DateTimeInterface $deleted = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['device:read'])]
    private string $title;

    #[ORM\Column(type: 'string', length: 10)]
    #[Groups(['device:read'])]
    private string $status;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['device:read'])]
    private string $location;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Groups(['device:read'])]
    private ?string $phoneNumber = null;

    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'devices')]
    #[Groups(['device:read'])]
    private Collection $categories;

    #[ORM\Column(nullable: true)]
    #[Groups(['device:read'])]
    private ?int $quantity = null;

    #[ORM\OneToMany(targetEntity: DevicePicture::class, mappedBy: 'device', cascade: ['persist', 'remove'])]
    #[Groups(['device:read'])]
    private Collection $devicePictures;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['device:read'])]
    private ?\DateTimeInterface $updated = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['device:read'])]
    private ?\DateTime $validated = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['device:read'])]
    private ?\DateTime $rejected = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->devicePictures = new ArrayCollection();
        $this->slug = Uuid::v1().time().uniqid();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(): self
    {
        $this->id = null;
        return $this;
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

    public function getParent(): ?Device
    {
        return $this->parent;
    }

    public function setParent(?Device $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getShowPhone(): ?bool
    {
        return $this->showPhone;
    }

    public function setShowPhone(?bool $showPhone): self
    {
        $this->showPhone = $showPhone;
        return $this;
    }

    public function getCreated(): \DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;
        return $this;
    }

    public function getDeleted(): ?\DateTimeInterface
    {
        return $this->deleted;
    }

    public function setDeleted(?\DateTimeInterface $deleted): self
    {
        $this->deleted = $deleted;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }


    /**
     * @return TypeDevice
     */
    public function getType(): TypeDevice
    {
        return $this->type;
    }

    /**
     * @param TypeDevice|null $type
     */
    public function setType(?TypeDevice $type): void
    {
        $this->type = $type;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function isShowPhone(): ?bool
    {
        return $this->showPhone;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addDevice($this);
        }

        return $this;
    }

    public function removeSubCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeDevice($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, DevicePicture>
     */
    public function getDevicePictures(): Collection
    {
        return $this->devicePictures;
    }
    public function addDevicePicture(DevicePicture $devicePicture): self
    {
        if (!$this->devicePictures->contains($devicePicture)) {
            $this->devicePictures[] = $devicePicture;
            $devicePicture->setDevice($this);
        }
        return $this;
    }

    public function removeDevicePicture(DevicePicture $devicePicture): self
    {
        if ($this->devicePictures->removeElement($devicePicture)) {
            if ($devicePicture->getDevice() === $this) {
                $devicePicture->setDevice(null);
            }
        }
        return $this;
    }
    public function isStatusValid(): bool{
        return $this->getStatus() === Constances::VALIDED;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTimeInterface $updated): static
    {
        $this->updated = $updated;

        return $this;
    }

    public function getValidated(): ?\DateTime
    {
        return $this->validated;
    }

    public function setValidated(?\DateTime $validated): static
    {
        $this->validated = $validated;

        return $this;
    }

    public function getRejected(): ?\DateTime
    {
        return $this->rejected;
    }

    public function setRejected(?\DateTime $rejected): static
    {
        $this->rejected = $rejected;

        return $this;
    }

}
