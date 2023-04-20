<?php

namespace Concrete\Core\Entity\User;

use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupRole;
use Concrete\Core\User\UserInfo;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="GroupRoleChanges"
 * )
 */
class GroupRoleChange implements SubjectInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int|null NULL if not yet flushed to the database
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Notification\GroupRoleChangeNotification", mappedBy="groupRoleChange", cascade={"remove"}),
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $notifications;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     *
     * @var int
     */
    protected $gID;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     *
     * @var int
     */
    protected $grID;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID", onDelete="SET NULL")
     *
     * @var \Concrete\Core\Entity\User\User|null
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $requested;

    /**
     * @param \Concrete\Core\User\Group\Group|null $group
     * @param \Concrete\Core\User\User|null $user
     * @param \Concrete\Core\User\Group\GroupRole|null $role
     */
    public function __construct($group = null, $user = null, $role = null)
    {
        $this->notifications = new ArrayCollection();
        if ($group instanceof Group) {
            $this->gID = $group->getGroupID();
        }
        if ($user instanceof \Concrete\Core\User\User) {
            $userInfo = $user->getUserInfoObject();
            if ($userInfo instanceof UserInfo) {
                $this->user = $userInfo->getEntityObject();
            }
        }
        if ($role instanceof GroupRole) {
            $this->grID = $role->getId();
        }
        $this->requested = new DateTime();
    }

    /**
     * @return \Concrete\Core\User\Group\Group|null
     */
    public function getGroup()
    {
        return Group::getByID($this->getGID());
    }

    /**
     * @return \Concrete\Core\User\Group\GroupRole|false
     */
    public function getRole()
    {
        return GroupRole::getByID($this->getGrID());
    }

    public function getGID(): int
    {
        return $this->gID;
    }

    /**
     * @return $this
     */
    public function setGID(int $gID): self
    {
        $this->gID = $gID;

        return $this;
    }

    public function getGrID(): int
    {
        return $this->grID;
    }

    /**
     * @return $this
     */
    public function setGrID(int $grID): self
    {
        $this->grID = $grID;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRequested(): DateTime
    {
        return $this->requested;
    }

    /**
     * @return $this
     */
    public function setRequested(DateTime $requested): self
    {
        $this->requested = $requested;

        return $this;
    }

    /**
     * Get the date of this notification.
     *
     * @return \DateTime
     */
    public function getNotificationDate()
    {
        return $this->requested;
    }

    public function getUsersToExcludeFromNotification()
    {
        return [];
    }

    /**
     * @return \Concrete\Core\Entity\Notification\GroupRoleChangeNotification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }
}
