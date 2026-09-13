<?php

namespace App\Entity;

use App\Repository\PortraitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PortraitRepository::class)]
class Portrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $alt = null;

    #[ORM\OneToOne(mappedBy: 'portrait', cascade: ['persist', 'remove'])]
    private ?Identity $identity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): static
    {
        $this->alt = $alt;

        return $this;
    }

    public function getIdentity(): ?Identity
    {
        return $this->identity;
    }

    public function setIdentity(Identity $identity): static
    {
        // set the owning side of the relation if necessary
        if ($identity->getPortrait() !== $this) {
            $identity->setPortrait($this);
        }

        $this->identity = $identity;

        return $this;
    }
}
