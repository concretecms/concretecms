<?php
namespace Concrete\Core\Entity\User;

use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\UserInfo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="GroupSignupRequestDeclines"
 * )
 */
class GroupSignupRequestDecline implements SubjectInterface
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Notification\GroupSignupRequestDeclineNotification", mappedBy="signup", cascade={"remove"}),
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
    protected $RequestDeclineed = null;

    /**
     * GroupSignupRequestDecline constructor.
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

        $this->RequestDeclineed = new \DateTime();
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
     * @return GroupSignupRequestDecline
     */
    public function setManager(User $manager): GroupSignupRequestDecline
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
     * @return GroupSignupRequestDecline
     */
    public function setId(int $id): GroupSignupRequestDecline
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
     * @return GroupSignupRequestDecline
     */
    public function setGID(int $gID): GroupSignupRequestDecline
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
     * @return GroupSignupRequestDecline
     */
    public function setUser(User $user): GroupSignupRequestDecline
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRequestDeclineed(): \DateTime
    {
        return $this->RequestDeclineed;
    }

    /**
     * @param \DateTime $RequestDeclineed
     * @return GroupSignupRequestDecline
     */
    public function setRequestDeclineed(\DateTime $RequestDeclineed): GroupSignupRequestDecline
    {
        $this->RequestDeclineed = $RequestDeclineed;
        return $this;
    }

    /**
     * Get the date of this notification
     *
     * @return \DateTime
     */
    public function getNotificationDate()
    {
        return $this->RequestDeclineed;
    }

    public function getUsersToExcludeFromNotification()
    {
        return [];
    }
}