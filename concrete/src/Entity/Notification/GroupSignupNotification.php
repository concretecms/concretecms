<?php

namespace Concrete\Core\Entity\Notification;

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
     *
     * @var \Concrete\Core\Entity\User\GroupSignup
     */
    protected $signup;

    /**
     * @param \Concrete\Core\Entity\User\GroupSignup $signup
     */
    public function __construct($signup)
    {
        $this->signup = $signup;
        parent::__construct($signup);
    }

    /**
     * @return \Concrete\Core\Entity\User\GroupSignup
     */
    public function getSignup()
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
        return new GroupSignupListView($this);
    }
}
