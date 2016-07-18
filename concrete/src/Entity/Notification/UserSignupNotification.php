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
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User"),
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
     */
    protected $user;

    /**
     * UserSignupNotification constructor.
     * @param $signup UserSignup
     */
    public function __construct(SubjectInterface $signup)
    {
        $this->user = $signup->getUser();
        parent::__construct($signup);
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

    public function getListView()
    {
        return new UserSignupListView($this);
    }


}
