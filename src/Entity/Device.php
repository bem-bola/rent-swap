<?php
namespace App\Entity;

use App\Repository\DeviceRepository;
use App\Service\Constances;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeviceRepository::class)]
class Device
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Device::class, cascade: ['persist'])]
    private ?Device $parent = null;

    #[ORM\ManyToOne(targetEntity: TypeDevice::class)]
    #[ORM\JoinColumn(nullable: false)]
    private TypeDevice $type;

    #[ORM\Column(type: 'string', length: 250)]
    private string $slug;

    #[ORM\Column(type: 'text')]
    private string $body;

    #[ORM\Column(type: 'float')]
    private float $price;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $showPhone = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $created;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $deleted = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $title;

    #[ORM\Column(type: 'string', length: 10)]
    private string $status;

    #[ORM\Column(type: 'string', length: 100)]
    private string $location;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'devices')]
    private Collection $categories;

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;

    #[ORM\OneToMany(targetEntity: DevicePicture::class, mappedBy: 'device', cascade: ['persist', 'remove'])]
    private Collection $devicePictures;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

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
}
