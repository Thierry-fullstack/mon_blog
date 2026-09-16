<?php

namespace App\Entity;

use App\Repository\IdentityRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IdentityRepository::class)]
class Identity
{
    const string FORM_ADD_SUCCESSFULLY = 'FORM_ADD_SUCCESSFULLY';
    const string FORM_BAD_RESPONSE = 'FORM_BAD_RESPONSE';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $skill = null;

    #[ORM\Column(length: 50)]
    private ?string $pseudo = null;

    #[ORM\OneToOne(inversedBy: 'identity', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Portrait $portrait = null;

    #[ORM\ManyToOne(inversedBy: 'identities')]
    private ?Region $region = null;

    #[ORM\OneToOne(inversedBy: 'identity',cascade: ['persist','remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    #[ORM\ManyToOne(inversedBy: 'identities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Civility $civility = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getSkill(): ?string
    {
        return $this->skill;
    }

    public function setSkill(string $skill): static
    {
        $this->skill = $skill;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPortrait(): ?Portrait
    {
        return $this->portrait;
    }

    public function setPortrait(Portrait $portrait): static
    {
        $this->portrait = $portrait;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): void
    {
        $this->client = $client;
    }

    public function __toString():string
    {
        return $this->getPseudo();
    }

    public function getCivility(): ?Civility
    {
        return $this->civility;
    }

    public function setCivility(?Civility $civility): static
    {
        $this->civility = $civility;

        return $this;
    }



}
