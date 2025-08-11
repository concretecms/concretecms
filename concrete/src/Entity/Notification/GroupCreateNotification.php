<?php

namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Notification\View\GroupCreateListView;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="GroupCreateNotifications"
 * )
 */
class GroupCreateNotification extends Notification
{
    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\GroupCreate", cascade={"persist", "remove"}, inversedBy="notifications"),
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     *
     * @var \Concrete\Core\Entity\User\GroupCreate
     */
    protected $create;

    /**
     * @param \Concrete\Core\Entity\User\GroupCreate $create
     */
    public function __construct($create)
    {
        $this->create = $create;
        parent::__construct($create);
    }

    /**
     * @return \Concrete\Core\Entity\User\GroupCreate
     */
    public function getCreate()
    {
        return $this->create;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Entity\Notification\Notification::getListView()
     */
    public function getListView()
    {
        return new GroupCreateListView($this);
    }
}
