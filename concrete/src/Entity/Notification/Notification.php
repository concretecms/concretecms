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
     */
    protected $nID;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $nDate = null;

    /**
     * @ORM\OneToMany(targetEntity="Concrete\Core\Entity\Notification\NotificationAlert", cascade={"remove"}, mappedBy="notification")
     * @ORM\JoinColumn(name="nID", referencedColumnName="nID")
     */
    protected $alerts;


    public function __construct(SubjectInterface $subject)
    {
        $this->nDate = $subject->getNotificationDate();
    }

    public function getNotificationID()
    {
        return $this->nID;
    }

    /**
     * @return mixed
     */
    public function getNotificationDate()
    {
        return $this->nDate;
    }

    /**
     * @param mixed $nDate
     */
    public function setNotificationDate($nDate)
    {
        $this->nDate = $nDate;
    }

    abstract public function getListView();

    /**
     * @return mixed
     */
    public function getAlerts()
    {
        return $this->alerts;
    }



}