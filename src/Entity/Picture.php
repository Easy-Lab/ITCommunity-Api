<?php

namespace App\Entity;

use App\Traits\IdColumnTrait;
use App\Traits\TimeAwareTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="App\Repository\PictureRepository")
 *
 * @JMS\ExclusionPolicy("ALL")
 */
class Picture
{
    use IdColumnTrait;
    use TimeAwareTrait;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="pictures")
     *
     * @JMS\Expose
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Expose
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Expose
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Expose
     */
    private $hash;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

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
