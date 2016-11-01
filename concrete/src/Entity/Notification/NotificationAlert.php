<?php
namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Notification\Formatter\StandardFormatter;
use Concrete\Core\Notification\Formatter\UserSignupFormatter;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\View\ListableInterface;
use Concrete\Core\Notification\View\ListViewPopulatorInterface;
use Concrete\Core\Notification\View\UserSignupView;
use Concrete\Core\Notification\View\ViewInterface;
use Doctrine\Common\Collections\ArrayCollection;
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
     */
    protected $naID;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Notification\Notification", inversedBy="alerts")
     * @ORM\JoinColumn(name="nID", referencedColumnName="nID")
     */
    protected $notification;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $naIsArchived = false;

    /**
     * @return mixed
     */
    public function isNotificationArchived()
    {
        return $this->naIsArchived;
    }

    /**
     * @param mixed $nIsArchived
     */
    public function setNotificationIsArchived($naIsArchived)
    {
        $this->naIsArchived = $naIsArchived;
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
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @param mixed $notification
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return mixed
     */
    public function getNotificationAlertID()
    {
        return $this->naID;
    }


}