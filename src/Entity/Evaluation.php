<?php

namespace App\Entity;

use App\Traits\IdColumnTrait;
use App\Traits\TimeAwareTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="App\Repository\EvaluationRepository")
 *
 * @JMS\ExclusionPolicy("ALL")
 */
class Evaluation
{
    use IdColumnTrait;
    use TimeAwareTrait;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="evaluations")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank
     *
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Contact", inversedBy="evaluations")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose
     */
    private $contact;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose
     */
    private $rating;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose
     */
    private $feedback;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $Contact): self
    {
        $this->contact = $Contact;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getFeedback(): ?string
    {
        return $this->feedback;
    }

    public function setFeedback(string $feedback): self
    {
        $this->feedback = $feedback;

        return $this;
    }
}
