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
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="date")
     *
     * @Assert\NotBlank
     *
     * @JMS\Expose
     */
    protected $publicationDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_component;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $company_component;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $other_information_component;

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
     * @return \DateTimeInterface|null
     */
    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    /**
     * @param \DateTimeInterface $publicationDate
     *
     * @return Review
     */
    public function setPublicationDate(?\DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

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

    public function getNameComponent(): ?string
    {
        return $this->name_component;
    }

    public function setNameComponent(string $name_component): self
    {
        $this->name_component = $name_component;

        return $this;
    }

    public function getCompanyComponent(): ?string
    {
        return $this->company_component;
    }

    public function setCompanyComponent(string $company_component): self
    {
        $this->company_component = $company_component;

        return $this;
    }

    public function getOtherInformationComponent(): ?string
    {
        return $this->other_information_component;
    }

    public function setOtherInformationComponent(?string $other_information_component): self
    {
        $this->other_information_component = $other_information_component;

        return $this;
    }
}
