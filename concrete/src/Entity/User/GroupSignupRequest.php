<?php
namespace Concrete\Core\Entity\User;

use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\UserInfo;
use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
* @ORM\Table(
*     name="GroupSignupRequests"
* )
*/
class GroupSignupRequest implements SubjectInterface
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Notification\GroupSignupRequestNotification", mappedBy="signup", cascade={"remove"}),
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
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $requested = null;

    /**
     * GroupSignupRequest constructor.
     * @param Group $group
     * @param \Concrete\Core\User\User $user
     * @throws \Exception
     */
    public function __construct($group = null, $user = null)
    {
        if ($group instanceof Group) {
            $this->gID = $group->getGroupID();
        }

        if ($user instanceof \Concrete\Core\User\User) {
            if ($user->getUserInfoObject() instanceof UserInfo) {
                $this->user = $user->getUserInfoObject()->getEntityObject();
            }
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

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return GroupSignupRequest
     */
    public function setId(int $id): GroupSignupRequest
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
     * @return GroupSignupRequest
     */
    public function setGID(int $gID): GroupSignupRequest
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
     * @return GroupSignupRequest
     */
    public function setUser(User $user): GroupSignupRequest
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
     * @return GroupSignupRequest
     */
    public function setRequested(\DateTime $requested): GroupSignupRequest
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