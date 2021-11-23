<?php
namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Entity\User\GroupCreate;
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
     */
    protected $create;

    /**
     * GroupEnterNotification constructor.
     * @param GroupCreate $create
     */
    public function __construct($create)
    {
        $this->create = $create;
        parent::__construct($create);
    }

    /**
     * @return GroupCreate
     */
    public function getCreate()
    {
        return $this->create;
    }

    public function getListView()
    {
        return new GroupCreateListView($this);
    }

}
