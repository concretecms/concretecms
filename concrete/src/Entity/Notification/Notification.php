<?php

namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Notification\Subject\SubjectInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\Table(
 *     name="Notifications"
 * )
 */
abstract class Notification
{
    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int|null NULL if not yet flushed to the database
     */
    protected $nID;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $nDate;

    /**
     * @ORM\OneToMany(targetEntity="Concrete\Core\Entity\Notification\NotificationAlert", cascade={"remove"}, mappedBy="notification")
     * @ORM\JoinColumn(name="nID", referencedColumnName="nID")
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $alerts;

    public function __construct(SubjectInterface $subject)
    {
        $this->nID = null;
        $this->nDate = $subject->getNotificationDate();
        $this->alerts = new ArrayCollection();
    }

    /**
     * @return int|null NULL if not yet flushed to the database
     */
    public function getNotificationID()
    {
        return $this->nID;
    }

    /**
     * @return \DateTime
     */
    public function getNotificationDate()
    {
        return $this->nDate;
    }

    /**
     * @return string|null
     */
    public function getNotificationDateTimeZone()
    {
        $site = app('site')->getSite();

        return $site ? $site->getTimezone() : null;
    }

    /**
     * @param \DateTime $nDate
     */
    public function setNotificationDate($nDate)
    {
        $this->nDate = $nDate;
    }

    /**
     * @return \Concrete\Core\Notification\View\ListViewInterface|null
     */
    abstract public function getListView();

    /**
     * @return \Doctrine\Common\Collections\Collection|\Concrete\Core\Entity\Notification\NotificationAlert[]
     */
    public function getAlerts()
    {
        return $this->alerts;
    }
}
