<?php
namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Entity\User\UserSignup;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\View\UserSignupListView;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="UserSignupNotifications"
 * )
 */
class UserSignupNotification extends Notification
{

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\UserSignup", cascade={"persist", "remove"}, inversedBy="notifications"),
     * @ORM\JoinColumn(name="usID", referencedColumnName="usID")
     */
    protected $signup;

    /**
     * UserSignupNotification constructor.
     * @param $signup UserSignup
     */
    public function __construct(SubjectInterface $signup)
    {
        $this->signup = $signup;
        parent::__construct($signup);
    }

    /**
     * @return mixed
     */
    public function getSignupRequest()
    {
        return $this->signup;
    }


    public function getListView()
    {
        return new UserSignupListView($this);
    }


}
