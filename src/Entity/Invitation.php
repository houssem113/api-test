<?php

namespace App\Entity;

use App\Core\Invitation\InvitationStatus;
use App\Repository\InvitationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: InvitationRepository::class)]
class Invitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['invitation:read'])]
    private ?int $id = null;

    #[Groups(['invitation:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $object = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[Assert\NotNull]
    #[Assert\Type('DateTimeInterface')]
    #[ORM\Column]
    private ?\DateTimeImmutable $scheduledAt = null;

    #[Groups(['invitation:read'])]
    #[ORM\ManyToOne(inversedBy: 'sentInvitations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;
     
    #[Groups(['invitation:read'])]
    #[Assert\NotNull]
    #[ORM\ManyToOne(inversedBy: 'receivedInvitations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $invited = null;

    #[Groups(['invitation:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?int $status = null;

    #[Groups(['invitation:read'])]
    private ?string $statusStr;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(?string $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
  
    #[ORM\PrePersist]
     public function onPrePersist() {

        $this->setCreatedAt();

        $this->setStatus(InvitationStatus::ON_HOLD);
     }
   

    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTimeImmutable();

        return $this;
    }

    public function getScheduledAt(): ?\DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(\DateTimeImmutable $scheduledAt): self
    {
        $this->scheduledAt = $scheduledAt;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getInvited(): ?User
    {
        return $this->invited;
    }

    public function setInvited(?User $invited): self
    {
        $this->invited = $invited;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of statusStr
     */ 
    public function getStatusStr()
    {
        return InvitationStatus::resolve($this->status);
    }
}
