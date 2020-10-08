<?php

namespace App\Entity;

use App\Repository\RiotServerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RiotServerRepository::class)
 * @UniqueEntity(
 *     fields={"name","api_route"},
 *     message="fields must be unique"
 * )
 */
class RiotServer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"riotServer"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255,unique=true)
     * @Assert\NotBlank(message="Name cannot be empty")
     * @Serializer\Groups({"riotServer"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255,unique=true)
     * @Assert\NotBlank(message="api_route cannot be empty")
     * @Serializer\Groups({"riotServer"})
     */
    private $api_route;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="riot_server_id")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getApiRoute(): ?string
    {
        return $this->api_route;
    }

    public function setApiRoute(string $api_route): self
    {
        $this->api_route = $api_route;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setRiotServerId($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getRiotServerId() === $this) {
                $user->setRiotServerId(null);
            }
        }

        return $this;
    }
}
