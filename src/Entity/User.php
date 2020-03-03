<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="app_user")
 *
 * @UniqueEntity({"email"}, message="Email already exists.")
 *
 * @JMS\ExclusionPolicy("ALL")
 */
class User extends AbstractUser implements UserInterface
{

    /**
     * @var ArrayCollection|Review[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Review", mappedBy="author", cascade={"persist", "remove"})
     *
     * @JMS\Expose
     * @JMS\Groups({"profile","reviews"})
     */
    protected $reviews;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Point", mappedBy="user", cascade={"persist", "remove"})
     */
    private $points;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="user", orphanRemoval=true)
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Evaluation", mappedBy="user", orphanRemoval=true)
     */
    private $evaluations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PublicChat", mappedBy="user", orphanRemoval=true)
     */
    private $publicChats;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PrivateChat", mappedBy="firstUser", orphanRemoval=true)
     */
    private $privateChats;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Picture", mappedBy="user")
     * @JMS\Expose
     * @JMS\Groups({"profile","pictures"})
     */
    private $pictures;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->points = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
        $this->publicChats = new ArrayCollection();
        $this->privateChats = new ArrayCollection();
        $this->pictures = new ArrayCollection();
    }

    /**
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    /**
     * @param Review $review
     *
     * @return User
     */
    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setAuthor($this);
        }

        return $this;
    }

    /**
     * @param Review $review
     *
     * @return User
     */
    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getAuthor() === $this) {
                $review->setAuthor(null);
            }
        }

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
            $point->setUser($this);
        }

        return $this;
    }

    public function removePoint(Point $point): self
    {
        if ($this->points->contains($point)) {
            $this->points->removeElement($point);
            // set the owning side to null (unless already changed)
            if ($point->getUser() === $this) {
                $point->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Evaluation[]
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): self
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations[] = $evaluation;
            $evaluation->setUser($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->contains($evaluation)) {
            $this->evaluations->removeElement($evaluation);
            // set the owning side to null (unless already changed)
            if ($evaluation->getUser() === $this) {
                $evaluation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PublicChat[]
     */
    public function getPublicChats(): Collection
    {
        return $this->publicChats;
    }

    public function addPublicChat(PublicChat $publicChat): self
    {
        if (!$this->publicChats->contains($publicChat)) {
            $this->publicChats[] = $publicChat;
            $publicChat->setUser($this);
        }

        return $this;
    }

    public function removePublicChat(PublicChat $publicChat): self
    {
        if ($this->publicChats->contains($publicChat)) {
            $this->publicChats->removeElement($publicChat);
            // set the owning side to null (unless already changed)
            if ($publicChat->getUser() === $this) {
                $publicChat->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PrivateChat[]
     */
    public function getPrivateChats(): Collection
    {
        return $this->privateChats;
    }

    public function addPrivateChat(PrivateChat $privateChat): self
    {
        if (!$this->privateChats->contains($privateChat)) {
            $this->privateChats[] = $privateChat;
            $privateChat->setFirstUser($this);
        }

        return $this;
    }

    public function removePrivateChat(PrivateChat $privateChat): self
    {
        if ($this->privateChats->contains($privateChat)) {
            $this->privateChats->removeElement($privateChat);
            // set the owning side to null (unless already changed)
            if ($privateChat->getFirstUser() === $this) {
                $privateChat->setFirstUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Picture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setUser($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->contains($picture)) {
            $this->pictures->removeElement($picture);
            // set the owning side to null (unless already changed)
            if ($picture->getUser() === $this) {
                $picture->setUser(null);
            }
        }

        return $this;
    }

}
