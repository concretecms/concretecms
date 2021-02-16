<?php
namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Entity\User\GroupSignup;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\View\GroupSignupListView;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="GroupSignupNotifications"
 * )
 */
class GroupSignupNotification extends Notification
{

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\GroupSignup", cascade={"persist", "remove"}, inversedBy="notifications"),
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    protected $signup;

    /**
     * GroupEnterNotification constructor.
     * @param GroupSignup $group
     */
    public function __construct($signup)
    {
        $this->signup = $signup;
        parent::__construct($signup);
    }

    /**
     * @return GroupSignup
     */
    public function getSignup()
    {
        return $this->signup;
    }

    public function getListView()
    {
        return new GroupSignupListView($this);
    }

}
