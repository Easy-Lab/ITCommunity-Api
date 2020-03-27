<?php

namespace App\Entity;

use App\Traits\IdColumnTrait;
use App\Traits\TimeAwareTraitPublic;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PrivateChatRepository")
 * @JMS\ExclusionPolicy("ALL")
 */
class PrivateChat
{
    use IdColumnTrait;
    use TimeAwareTraitPublic;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="privateChats")
     * @ORM\JoinColumn(nullable=false)
     *
     * @JMS\Expose
     * @JMS\Groups("user")
     */
    private $firstUser;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="privateChats")
     * @ORM\JoinColumn(nullable=false)
     *
     * @JMS\Expose
     * @JMS\Groups("user")
     */
    private $secondUser;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose
     */
    private $message;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstUser(): ?User
    {
        return $this->firstUser;
    }

    public function setFirstUser(?User $firstUser): self
    {
        $this->firstUser = $firstUser;

        return $this;
    }

    public function getSecondUser(): ?User
    {
        return $this->secondUser;
    }

    public function setSecondUser(?User $secondUser): self
    {
        $this->secondUser = $secondUser;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
