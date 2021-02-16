<?php
namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Entity\User\GroupSignupRequestDecline;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\View\GroupSignupRequestDeclineListView;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="GroupSignupRequestDeclineNotifications"
 * )
 */
class GroupSignupRequestDeclineNotification extends Notification
{

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\GroupSignupRequestDecline", cascade={"persist", "remove"}, inversedBy="notifications"),
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    protected $signup;

    /**
     * GroupEnterNotification constructor.
     * @param GroupSignupRequestDecline $group
     */
    public function __construct($signup)
    {
        $this->signup = $signup;
        parent::__construct($signup);
    }

    /**
     * @return GroupSignupRequestDecline
     */
    public function getSignupRequestDecline()
    {
        return $this->signup;
    }

    public function getListView()
    {
        return new GroupSignupRequestDeclineListView($this);
    }

}
