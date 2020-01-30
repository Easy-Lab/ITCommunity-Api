<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdColumnTrait;
use App\Traits\TimeAwareTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReviewRepository")
 *
 * @JMS\ExclusionPolicy("ALL")
 */
class Review
{
    use IdColumnTrait;
    use TimeAwareTrait;

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
     * @JMS\Expose
     * @JMS\Groups("author")
     */
    protected $author;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameComponent;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $companyComponent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $otherInformationComponent;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

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
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null $author
     *
     * @return Review
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

}
