<?php

namespace App\Entity;

use App\Repository\SupportTicketRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupportTicketRepository::class)]
class SupportTicket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adminNote = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Order $order = null;

    public function __construct()
    {
        $this->status = 'open';
        $this->createdAt = new \DateTimeImmutable();
    }
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAdminNote(): ?string
    {
        return $this->adminNote;
    }

    public function setAdminNote(?string $adminNote): static
    {
        $this->adminNote = $adminNote;

        return $this;
    }
}
