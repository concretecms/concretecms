<?php
namespace Concrete\Core\Entity\User;

use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupRole;
use Concrete\Core\User\UserInfo;
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
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Notification\GroupRoleChangeNotification", mappedBy="signup", cascade={"remove"}),
     */
    protected $notifications;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $gID;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $grID;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID", onDelete="SET NULL")
     */
    protected $user;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $requested = null;

    /**
     * GroupSignupRequest constructor.
     * @param Group $group
     * @param \Concrete\Core\User\User $user
     * @param GroupRole $role
     * @throws \Exception
     */
    public function __construct($group = null, $user = null, $role = null)
    {
        if ($group instanceof Group) {
            $this->gID = $group->getGroupID();
        }

        if ($user instanceof \Concrete\Core\User\User) {
            if ($user->getUserInfoObject() instanceof UserInfo) {
                $this->user = $user->getUserInfoObject()->getEntityObject();
            }
        }

        if ($role instanceof GroupRole) {
            $this->grID = $role->getId();
        }

        $this->requested = new \DateTime();
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return Group::getByID($this->getGID());
    }

    public function getRole() {
        return GroupRole::getByID($this->getGrID());
    }

    /**
     * @return int
     */
    public function getGID(): int
    {
        return $this->gID;
    }

    /**
     * @param int $gID
     * @return GroupSignup
     */
    public function setGID(int $gID): GroupSignup
    {
        $this->gID = $gID;
        return $this;
    }

    /**
     * @return int
     */
    public function getGrID(): int
    {
        return $this->grID;
    }

    /**
     * @param int $grID
     * @return GroupRoleChange
     */
    public function setGrID(int $grID): GroupRoleChange
    {
        $this->grID = $grID;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return GroupSignup
     */
    public function setUser(User $user): GroupSignup
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRequested(): \DateTime
    {
        return $this->requested;
    }

    /**
     * @param \DateTime $requested
     * @return GroupSignup
     */
    public function setRequested(\DateTime $requested): GroupSignup
    {
        $this->requested = $requested;
        return $this;
    }


    /**
     * Get the date of this notification
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
}