<?php

namespace Concrete\Core\Entity\User;

use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\UserInfo;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int|null NULL if not yet flushed to the database
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Notification\GroupSignupRequestAcceptNotification", mappedBy="signup", cascade={"remove"}),
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
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID", onDelete="SET NULL")
     *
     * @var \Concrete\Core\Entity\User\User|null
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="managerUID", referencedColumnName="uID", onDelete="SET NULL")
     *
     * @var \Concrete\Core\Entity\User\User|null
     */
    protected $manager;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $RequestAccepted;

    /**
     * @param \Concrete\Core\User\Group\Group|null $group
     * @param \Concrete\Core\User\User|null $user
     * @param \Concrete\Core\User\User|null $manager
     */
    public function __construct($group = null, $user = null, $manager = null)
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
        if ($manager instanceof \Concrete\Core\User\User) {
            $userInfo = $manager->getUserInfoObject();
            if ($userInfo instanceof UserInfo) {
                $this->manager = $userInfo->getEntityObject();
            }
        }
        $this->RequestAccepted = new DateTime();
    }

    public function getManager(): ?User
    {
        return $this->manager;
    }

    /**
     * @return $this
     */
    public function setManager(User $manager): self
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @return \Concrete\Core\User\Group\Group|null
     */
    public function getGroup()
    {
        return Group::getByID($this->getGID());
    }

    /**
     * @return int|null return NULL if not yet flushed to the database
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getRequestAccepted(): DateTime
    {
        return $this->RequestAccepted;
    }

    /**
     * @return $this
     */
    public function setRequestAccepted(DateTime $RequestAccepted): self
    {
        $this->RequestAccepted = $RequestAccepted;

        return $this;
    }

    /**
     * Get the date of this notification.
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

    /**
     * @return \Concrete\Core\Entity\Notification\GroupSignupRequestAcceptNotification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }
}
