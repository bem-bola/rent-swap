<?php

namespace App\Entity;

use App\Repository\ReservationStatusRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationStatusRepository::class)]
class ReservationStatus
{
    const LIBELLE = ['pending', 'accepted', 'rejected', 'cancelled'];
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 150)]
    private string $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        if(is_array(self::LIBELLE)) $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

}
