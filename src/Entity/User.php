<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_banned;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $riot_pseudo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $riot_account_id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_riot_validated;

    /**
     * @ORM\ManyToOne(targetEntity=RiotServer::class, inversedBy="users")
     */
    private $riot_server_id;

    /**
     * @ORM\OneToMany(targetEntity=Ban::class, mappedBy="user_id", orphanRemoval=true)
     */
    private $bans;

    public function __construct()
    {
        $this->bans = new ArrayCollection();
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
    public function getUsername(): string
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
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getIsBanned(): ?bool
    {
        return $this->is_banned;
    }

    public function setIsBanned(bool $is_banned): self
    {
        $this->is_banned = $is_banned;

        return $this;
    }

    public function getRiotPseudo(): ?string
    {
        return $this->riot_pseudo;
    }

    public function setRiotPseudo(string $riot_pseudo): self
    {
        $this->riot_pseudo = $riot_pseudo;

        return $this;
    }

    public function getRiotAccountId(): ?string
    {
        return $this->riot_account_id;
    }

    public function setRiotAccountId(string $riot_account_id): self
    {
        $this->riot_account_id = $riot_account_id;

        return $this;
    }

    public function getIsRiotValidated(): ?bool
    {
        return $this->is_riot_validated;
    }

    public function setIsRiotValidated(bool $is_riot_validated): self
    {
        $this->is_riot_validated = $is_riot_validated;

        return $this;
    }

    public function getRiotServerId(): ?RiotServer
    {
        return $this->riot_server_id;
    }

    public function setRiotServerId(?RiotServer $riot_server_id): self
    {
        $this->riot_server_id = $riot_server_id;

        return $this;
    }

    /**
     * @return Collection|Ban[]
     */
    public function getBans(): Collection
    {
        return $this->bans;
    }

    public function addBan(Ban $ban): self
    {
        if (!$this->bans->contains($ban)) {
            $this->bans[] = $ban;
            $ban->setUserId($this);
        }

        return $this;
    }

    public function removeBan(Ban $ban): self
    {
        if ($this->bans->contains($ban)) {
            $this->bans->removeElement($ban);
            // set the owning side to null (unless already changed)
            if ($ban->getUserId() === $this) {
                $ban->setUserId(null);
            }
        }

        return $this;
    }
}
