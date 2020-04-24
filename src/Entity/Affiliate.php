<?php

namespace App\Entity;

use App\Traits\IdColumnTrait;
use App\Traits\TimeAwareTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="App\Repository\AffiliateRepository")
 *
 * @UniqueEntity({"email"}, message="User with this email alredy have invitation.")
 * @JMS\ExclusionPolicy("ALL")
 */
class Affiliate
{
    use IdColumnTrait;
    use TimeAwareTrait;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_AFFILIATE', object)"
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_AFFILIATE', object)"
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Email
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_AFFILIATE', object)"
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_AFFILIATE', object)"
     * )
     */
    private $body;

    /**
     * @ORM\Column(type="boolean")
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_AFFILIATE', object)"
     * )
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_AFFILIATE', object)"
     * )
     */
    private $hash;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="affiliates")
     *
     * @JMS\Expose
     * @JMS\Groups("user")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Point", mappedBy="affiliate", cascade={"persist", "remove"})
     *
     * @JMS\Expose
     * @JMS\Groups("points")
     */
    private $points;

    public function __construct()
    {
        $this->points = new ArrayCollection();
    }

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

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

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
     * @return Collection|Point[]
     */
    public function getPoints(): Collection
    {
        return $this->points;
    }

    public function addPoint(Point $point): self
    {
        if (!$this->points->contains($point)) {
            $this->points[] = $point;
            $point->setAffiliate($this);
        }

        return $this;
    }

    public function removePoint(Point $point): self
    {
        if ($this->points->contains($point)) {
            $this->points->removeElement($point);
            // set the owning side to null (unless already changed)
            if ($point->getAffiliate() === $this) {
                $point->setAffiliate(null);
            }
        }

        return $this;
    }
}
