<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    #[Groups(['device:read', 'category:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 150)]
    #[Groups(['device:read', 'category:read'])]
    #[Assert\NotBlank()]
    #[Assert\Regex(
        pattern: '/^[A-Za-zÀ-ÖØ-öø-ÿ]{2,}$/',
        message: 'Le nom doit contenir au moins 2 caractères alphabétiques.'
    )]
    private string $name;

    #[ORM\Column(type: Types::GUID)]
    #[Groups(['device:read', 'category:read'])]
    private ?string $slug = null;

    #[ORM\ManyToMany(targetEntity: Device::class, inversedBy: 'devices', cascade: ['persist'])]
    private Collection $devices;

    public function __construct()
    {
        $this->devices = new ArrayCollection();
        $this->slug = Uuid::v1();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
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

    /**
     * @return Collection<int, Device>
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function addDevice(Device $device): static
    {
        if (!$this->devices->contains($device)) {
            $this->devices->add($device);
        }

        return $this;
    }

    public function removeDevice(Device $device): static
    {
        $this->devices->removeElement($device);

        return $this;
    }

}
