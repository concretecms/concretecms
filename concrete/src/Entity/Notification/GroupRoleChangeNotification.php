<?php

namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\View\GroupRoleChangeListView;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="GroupRoleChangeNotifications"
 * )
 */
class GroupRoleChangeNotification extends Notification
{
    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\GroupRoleChange", cascade={"persist", "remove"}, inversedBy="notifications"),
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     *
     * @var \Concrete\Core\Entity\User\GroupRoleChange
     */
    protected $groupRoleChange;

    /**
     * @param \Concrete\Core\Entity\User\GroupRoleChange $groupRoleChange
     */
    public function __construct($groupRoleChange)
    {
        $this->groupRoleChange = $groupRoleChange;
        parent::__construct($groupRoleChange);
    }

    /**
     * @return \Concrete\Core\Entity\User\GroupRoleChange
     */
    public function getGroupRoleChange(): SubjectInterface
    {
        return $this->groupRoleChange;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Entity\Notification\Notification::getListView()
     */
    public function getListView()
    {
        return new GroupRoleChangeListView($this);
    }
}
