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
     * @ORM\ManyToMany(targetEntity="Concrete\Core\Entity\User\User", inversedBy="notifications", cascade={"persist"})
     * @ORM\JoinTable(name="NotificationUsers",
     * joinColumns={@ORM\JoinColumn(name="nID", referencedColumnName="nID")},
     * inverseJoinColumns={@ORM\JoinColumn(name="uID", referencedColumnName="uID")}
     * )
     */
    protected $alerted;

    /**
     * @return mixed
     */
    public function getUsersToAlert()
    {
        return $this->alerted;
    }

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
     * @ORM\Column(type="boolean")
     */
    protected $nIsArchived = false;

    public function __construct(SubjectInterface $subject)
    {
        $this->nDate = $subject->getNotificationDate();
        $this->alerted = new ArrayCollection();
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

    /**
     * @return mixed
     */
    public function isNotificationArchived()
    {
        return $this->nIsArchived;
    }

    /**
     * @param mixed $nIsArchived
     */
    public function setNotificationIsArchived($nIsArchived)
    {
        $this->nIsArchived = $nIsArchived;
    }

    abstract public function getListView();

}