<?php

namespace Concrete\Core\Entity\Notification;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\Concrete\Core\Entity\Notification\NotificationAlertRepository")
 * @ORM\Table(
 *     name="NotificationAlerts"
 * )
 */
class NotificationAlert
{
    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int|null NULL if not yet flushed to the database
     */
    protected $naID;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User", inversedBy="alerts")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
     *
     * @var \Concrete\Core\Entity\User\User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Notification\Notification", inversedBy="alerts")
     * @ORM\JoinColumn(name="nID", referencedColumnName="nID")
     *
     * @var \Concrete\Core\Entity\Notification\Notification
     */
    protected $notification;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $naIsArchived = false;

    /**
     * @return bool
     */
    public function isNotificationArchived()
    {
        return $this->naIsArchived;
    }

    /**
     * @param bool $naIsArchived
     */
    public function setNotificationIsArchived($naIsArchived)
    {
        $this->naIsArchived = $naIsArchived;
    }

    /**
     * @return \Concrete\Core\Entity\User\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \Concrete\Core\Entity\User\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return \Concrete\Core\Entity\Notification\Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @param \Concrete\Core\Entity\Notification\Notification $notification
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return int|null returns NULL if not yet flushed to the database
     */
    public function getNotificationAlertID()
    {
        return $this->naID;
    }
}
