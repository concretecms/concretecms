<?php

namespace Concrete\Core\Entity\Notification;

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
     *
     * @var \Concrete\Core\Entity\User\GroupSignupRequestAccept
     */
    protected $signup;

    /**
     * @param \Concrete\Core\Entity\User\GroupSignupRequestAccept $signup
     */
    public function __construct($signup)
    {
        $this->signup = $signup;
        parent::__construct($signup);
    }

    /**
     * @return \Concrete\Core\Entity\User\GroupSignupRequestAccept
     */
    public function getSignupRequestAccept()
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
        return new GroupSignupRequestAcceptListView($this);
    }
}
