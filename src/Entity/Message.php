<?php

namespace App\Entity;

use App\Traits\IdColumnTrait;
use App\Traits\TimeAwareTraitPublic;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 *
 * @JMS\ExclusionPolicy("ALL")
 */
class Message
{

    use IdColumnTrait;
    use TimeAwareTraitPublic;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Contact", inversedBy="messages")
     *
     * @JMS\Expose
     * @JMS\Groups("contact")
     */
    private $contact;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     *
     * @JMS\Expose
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose
     */
    private $question;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     */
    private $answer;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_MESSAGE', object)")
     */
    private $hash;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose
     * @JMS\Groups("user")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Evaluation", mappedBy="message")
     *
     * @JMS\Expose
     * @JMS\Groups("evaluations")
     */
    private $evaluations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Point", mappedBy="message", cascade={"persist", "remove"})
     *
     * @JMS\Expose
     * @JMS\Groups("points")
     */
    private $points;

    public function __construct()
    {
        $this->evaluations = new ArrayCollection();
        $this->points = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(?string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Evaluation[]
     */
    public function getEvaluation(): Collection
    {
        return $this->evaluations;
    }

    /**
     * @return Collection|Point[]
     */
    public function getPoint(): Collection
    {
        return $this->points;
    }
}
