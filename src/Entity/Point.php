<?php

namespace App\Entity;

use App\Traits\IdColumnTrait;
use App\Traits\TimeAwareTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="App\Repository\PointRepository")
 *
 * @JMS\ExclusionPolicy("ALL")
 */
class Point
{
    use IdColumnTrait;
    use TimeAwareTrait;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="points")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Message")
     *
     * @JMS\Expose
     */
    private $message;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Evaluation")
     *
     * @JMS\Expose
     */
    private $evaluation;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Review")
     *
     * @JMS\Expose
     */
    private $review;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getEvaluation(): ?Evaluation
    {
        return $this->evaluation;
    }

    public function setEvaluation(?Evaluation $evaluation): self
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): self
    {
        $this->review = $review;

        return $this;
    }
}
