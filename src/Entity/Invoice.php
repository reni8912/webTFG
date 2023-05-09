<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?UserB $userB = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?UserS $userS = null;

    #[ORM\Column]
    private ?float $money = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?InvoiceState $state = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserB(): ?UserB
    {
        return $this->userB;
    }

    public function setUserB(?UserB $userB): self
    {
        $this->userB = $userB;

        return $this;
    }

    public function getUserS(): ?UserS
    {
        return $this->userS;
    }

    public function setUserS(?UserS $userS): self
    {
        $this->userS = $userS;

        return $this;
    }

    public function getMoney(): ?float
    {
        return $this->money;
    }

    public function setMoney(float $money): self
    {
        $this->money = $money;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getState(): ?InvoiceState
    {
        return $this->state;
    }

    public function setState(?InvoiceState $state): self
    {
        $this->state = $state;

        return $this;
    }
}
