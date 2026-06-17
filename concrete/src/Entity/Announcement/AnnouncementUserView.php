<?php

namespace Concrete\Core\Entity\Announcement;

use Concrete\Core\Announcement\Controller\ControllerInterface;
use Concrete\Core\Announcement\Manager;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="AnnouncementUserViews")
 */
class AnnouncementUserView
{


    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Announcement")
     */
    protected $announcement;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $dateViewed;

    /**
     * Introduction constructor.
     */
    public function __construct()
    {
        $this->dateViewed = time();
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getAnnouncement()
    {
        return $this->announcement;
    }

    /**
     * @param mixed $announcement
     */
    public function setAnnouncement($announcement): void
    {
        $this->announcement = $announcement;
    }

    /**
     * @return int
     */
    public function getDateViewed(): int
    {
        return $this->dateViewed;
    }


}
