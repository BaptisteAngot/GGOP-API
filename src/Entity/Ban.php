<?php

namespace App\Entity;

use App\Repository\BanRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BanRepository::class)
 */
class Ban
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bans")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user_id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\NotBlank(message="start cannot be empty")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="end cannot be empty")
     */
    private $end;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="motive cannot be empty")
     */
    private $motive;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(?\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(?\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getMotive(): ?string
    {
        return $this->motive;
    }

    public function setMotive(?string $motive): self
    {
        $this->motive = $motive;

        return $this;
    }
}
