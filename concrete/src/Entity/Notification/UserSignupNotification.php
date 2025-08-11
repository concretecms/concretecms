<?php

namespace Concrete\Core\Entity\Notification;

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
     *
     * @var \Concrete\Core\Entity\User\UserSignup
     */
    protected $signup;

    /**
     * @param \Concrete\Core\Entity\User\UserSignup $signup
     */
    public function __construct(SubjectInterface $signup)
    {
        $this->signup = $signup;
        parent::__construct($signup);
    }

    /**
     * @return \Concrete\Core\Entity\User\UserSignup
     */
    public function getSignupRequest()
    {
        return $this->signup;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Entity\Notification\Notification::getListView()
     */
    public function getListView()
    {
        return new UserSignupListView($this);
    }
}
