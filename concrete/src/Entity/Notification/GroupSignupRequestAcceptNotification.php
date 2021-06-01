<?php
namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Entity\User\GroupSignupRequestAccept;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\View\GroupSignupRequestAcceptListView;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="GroupSignupRequestAcceptNotifications"
 * )
 */
class GroupSignupRequestAcceptNotification extends Notification
{

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\GroupSignupRequestAccept", cascade={"persist", "remove"}, inversedBy="notifications"),
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    protected $signup;

    /**
     * GroupEnterNotification constructor.
     * @param GroupSignupRequestAccept $group
     */
    public function __construct($signup)
    {
        $this->signup = $signup;
        parent::__construct($signup);
    }

    /**
     * @return GroupSignupRequestAccept
     */
    public function getSignupRequestAccept()
    {
        return $this->signup;
    }

    public function getListView()
    {
        return new GroupSignupRequestAcceptListView($this);
    }

}
