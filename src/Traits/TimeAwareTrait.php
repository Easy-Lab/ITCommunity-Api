<?php

declare(strict_types=1);

namespace App\Traits;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

trait TimeAwareTrait
{
    /**
     * @var DateTimeInterface
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_USER', object)"
     * )
     */
    protected $created;

    /**
     * @var DateTimeInterface
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     *
     * @JMS\Expose(
     *   if="service('security.authorization_checker').isGranted('CAN_UPDATE_USER', object)"
     * )
     */
    protected $updated;

    /**
     * Set created.
     *
     * @param DateTimeInterface|null $created
     *
     * @return mixed
     */
    public function setCreated(DateTimeInterface $created = null)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     *
     * @return DateTimeInterface
     */
    public function getCreated(): DateTimeInterface
    {
        return $this->created;
    }

    /**
     * Set updated.
     *
     * @param DateTimeInterface|null $updated
     *
     * @return mixed
     */
    public function setUpdated(DateTimeInterface $updated = null)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated.
     *
     * @return DateTimeInterface $updated
     */
    public function getUpdated(): DateTimeInterface
    {
        return $this->updated;
    }
    
}
