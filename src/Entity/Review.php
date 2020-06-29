<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdColumnTrait;
use App\Traits\TimeAwareTraitPublic;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="App\Repository\ReviewRepository")
 *
 * @JMS\ExclusionPolicy("ALL")
 */
class Review
{
    use IdColumnTrait;
    use TimeAwareTraitPublic;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose
     */
    protected $body;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose
     */
    protected $rating;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reviews", cascade={"persist", "remove"})
     *
     * @SWG\Property(ref=@Model(type=Review::class))
     *
     * @JMS\Expose
     * @JMS\Groups("user")
     */
    protected $user;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Expose
     */
    private $nameComponent;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Expose
     */
    private $companyComponent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Expose
     */
    private $otherInformationComponent;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Expose
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Point", mappedBy="review", cascade={"persist", "remove"})
     *
     * @JMS\Expose
     * @JMS\Groups("points")
     */
    private $points;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_REVIEW', object)")
     */
    private $hash;

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string $body
     *
     * @return Review
     */
    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     *
     * @return Review
     */
    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return Review
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNameComponent()
    {
        return $this->nameComponent;
    }

    /**
     * @param mixed $nameComponent
     */
    public function setNameComponent($nameComponent): void
    {
        $this->nameComponent = $nameComponent;
    }

    /**
     * @return mixed
     */
    public function getCompanyComponent()
    {
        return $this->companyComponent;
    }

    /**
     * @param mixed $companyComponent
     */
    public function setCompanyComponent($companyComponent): void
    {
        $this->companyComponent = $companyComponent;
    }

    /**
     * @return mixed
     */
    public function getOtherInformationComponent()
    {
        return $this->otherInformationComponent;
    }

    /**
     * @param mixed $otherInformationComponent
     */
    public function setOtherInformationComponent($otherInformationComponent): void
    {
        $this->otherInformationComponent = $otherInformationComponent;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Review
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Point[]
     */
    public function getPoint(): Collection
    {
        return $this->points;
    }

    /**
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     * @return Review
     */
    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

}
