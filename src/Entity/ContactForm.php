<?php

namespace App\Entity;

use App\Traits\IdColumnTrait;
use App\Traits\TimeAwareTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="App\Repository\ContactFormRepository")
 *
 * @JMS\ExclusionPolicy("ALL")
 */
class ContactForm
{
    use IdColumnTrait;
    use TimeAwareTrait;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_CONTACT_FORM', object)"
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_CONTACT_FORM', object)"
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_CONTACT_FORM', object)"
     * )
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Email
     * @Assert\NotBlank
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_CONTACT_FORM', object)"
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_CONTACT_FORM', object)"
     * )
     */
    private $subject;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_CONTACT_FORM', object)"
     * )
     */
    private $body;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_CONTACT_FORM', object)"
     * )
     */
    protected $hash;

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

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
}
