<?php

namespace App\Entity;

use App\Traits\IdColumnTrait;
use App\Traits\TimeAwareTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="App\Repository\BugReportRepository")
 *
 * @JMS\ExclusionPolicy("ALL")
 */
class BugReport
{
    use IdColumnTrait;
    use TimeAwareTrait;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_BUG_REPORT', object)"
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Email
     * @Assert\NotBlank
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_BUG_REPORT', object)"
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Expose
     */
    private $subject;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Expose
     */
    private $body;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Expose
     */
    private $hash;

    /**
     * @ORM\Column(type="boolean")
     *
     * @JMS\Expose
     */
    private $solved = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
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

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

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

    public function getSolved(): ?bool
    {
        return $this->solved;
    }

    public function setSolved(?bool $solved): self
    {
        $this->solved = $solved;

        return $this;
    }
}
