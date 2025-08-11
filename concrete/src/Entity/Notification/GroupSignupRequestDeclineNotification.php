<?php

namespace Concrete\Core\Entity\Notification;

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
     *
     * @var \Concrete\Core\Entity\User\GroupSignupRequestDecline
     */
    protected $signup;

    /**
     * @param \Concrete\Core\Entity\User\GroupSignupRequestDecline $signup
     */
    public function __construct($signup)
    {
        $this->signup = $signup;
        parent::__construct($signup);
    }

    /**
     * @return \Concrete\Core\Entity\User\GroupSignupRequestDecline
     */
    public function getSignupRequestDecline()
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
        return new GroupSignupRequestDeclineListView($this);
    }
}
