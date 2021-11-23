<?php
namespace Concrete\Core\Entity\User;

use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\UserInfo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="GroupSignupRequestAccepts"
 * )
 */
class GroupSignupRequestAccept implements SubjectInterface
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Notification\GroupSignupRequestAcceptNotification", mappedBy="signup", cascade={"remove"}),
     */
    protected $notifications;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $gID;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID", onDelete="SET NULL")
     */
    protected $user;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="managerUID", referencedColumnName="uID", onDelete="SET NULL")
     */
    protected $manager;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $RequestAccepted = null;

    /**
     * GroupSignupRequestAccept constructor.
     * @param Group $group
     * @param \Concrete\Core\User\User $user
     * @param \Concrete\Core\User\User $manager
     * @throws \Exception
     */
    public function __construct($group = null, $user = null, $manager = null)
    {
        if ($group instanceof Group) {
            $this->gID = $group->getGroupID();
        }

        if ($user instanceof \Concrete\Core\User\User) {
            if ($user->getUserInfoObject() instanceof UserInfo) {
                $this->user = $user->getUserInfoObject()->getEntityObject();
            }
        }

        if ($manager instanceof \Concrete\Core\User\User) {
            $this->manager = $user->getUserInfoObject()->getEntityObject();
        }

        $this->RequestAccepted = new \DateTime();
    }

    /**
     * @return User
     */
    public function getManager(): User
    {
        return $this->manager;
    }

    /**
     * @param User $manager
     * @return GroupSignupRequestAccept
     */
    public function setManager(User $manager): GroupSignupRequestAccept
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return Group::getByID($this->getGID());
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return GroupSignupRequestAccept
     */
    public function setId(int $id): GroupSignupRequestAccept
    {
        $this->id = $id;
        return $this;
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
     * @return GroupSignupRequestAccept
     */
    public function setGID(int $gID): GroupSignupRequestAccept
    {
        $this->gID = $gID;
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
     * @return GroupSignupRequestAccept
     */
    public function setUser(User $user): GroupSignupRequestAccept
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRequestAccepted(): \DateTime
    {
        return $this->RequestAccepted;
    }

    /**
     * @param \DateTime $RequestAccepted
     * @return GroupSignupRequestAccept
     */
    public function setRequestAccepted(\DateTime $RequestAccepted): GroupSignupRequestAccept
    {
        $this->RequestAccepted = $RequestAccepted;
        return $this;
    }

    /**
     * Get the date of this notification
     *
     * @return \DateTime
     */
    public function getNotificationDate()
    {
        return $this->RequestAccepted;
    }

    public function getUsersToExcludeFromNotification()
    {
        return [];
    }
}