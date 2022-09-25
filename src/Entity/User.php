<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;




#[UniqueEntity('email')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['invitation:read', "user:read"])]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotNull]
    #[Assert\Email]
    #[ORM\Column(length: 180, unique: true)]
    #[Groups(["user:read"])]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Assert\NotNull]
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotNull]
    #[Groups(['invitation:read', "user:read"])]
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[Assert\NotNull]
    #[Groups(['invitation:read', "user:read"])]
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Invitation::class, orphanRemoval: true)]
    private Collection $sentInvitations;

    #[ORM\OneToMany(mappedBy: 'invited', targetEntity: Invitation::class, orphanRemoval: true)]
    private Collection $receivedInvitations;

    public function __construct()
    {
        $this->sentInvitations = new ArrayCollection();
        $this->receivedInvitations = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

  
    public function __toString()
    {
        return $this->getId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getSentInvitations(): Collection
    {
        return $this->sentInvitations;
    }

    public function addSentInvitation(Invitation $sentInvitation): self
    {
        if (!$this->sentInvitations->contains($sentInvitation)) {
            $this->sentInvitations->add($sentInvitation);
            $sentInvitation->setSender($this);
        }

        return $this;
    }

    public function removeSentInvitation(Invitation $sentInvitation): self
    {
        if ($this->sentInvitations->removeElement($sentInvitation)) {
            // set the owning side to null (unless already changed)
            if ($sentInvitation->getSender() === $this) {
                $sentInvitation->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getReceivedInvitations(): Collection
    {
        return $this->receivedInvitations;
    }

    public function addReceivedInvitation(Invitation $receivedInvitation): self
    {
        if (!$this->receivedInvitations->contains($receivedInvitation)) {
            $this->receivedInvitations->add($receivedInvitation);
            $receivedInvitation->setInvited($this);
        }

        return $this;
    }

    public function removeReceivedInvitation(Invitation $receivedInvitation): self
    {
        if ($this->receivedInvitations->removeElement($receivedInvitation)) {
            // set the owning side to null (unless already changed)
            if ($receivedInvitation->getInvited() === $this) {
                $receivedInvitation->setInvited(null);
            }
        }

        return $this;
    }
}
