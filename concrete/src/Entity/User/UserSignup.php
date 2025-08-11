<?php

namespace Concrete\Core\Entity\User;

use Concrete\Core\Notification\Subject\SubjectInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="UserSignups"
 * )
 */
class UserSignup implements SubjectInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int|null NULL if not yet flushed to the database
     */
    protected $usID;

    /**
     * @ORM\OneToOne(targetEntity="\Concrete\Core\Entity\User\User", inversedBy="signup"),
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
     *
     * @var \Concrete\Core\Entity\User\User
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Notification\UserSignupNotification", mappedBy="signup", cascade={"remove"}),
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $notifications;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User"),
     * @ORM\JoinColumn(name="createdBy", referencedColumnName="uID")
     *
     * @var \Concrete\Core\Entity\User\User|null
     */
    protected $createdBy;

    public function __construct(User $user, ?User $createdBy = null)
    {
        $this->user = $user;
        $this->createdBy = $createdBy;
        $this->notifications = new ArrayCollection();
    }

    /**
     * @return \Concrete\Core\Entity\User\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \DateTime
     */
    public function getNotificationDate()
    {
        return $this->user->getUserDateAdded();
    }

    /**
     * @return \Concrete\Core\Entity\User\User|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $user)
    {
        $this->createdBy = $user;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Notification\Subject\SubjectInterface::getUsersToExcludeFromNotification()
     */
    public function getUsersToExcludeFromNotification()
    {
        return is_object($this->createdBy) ? [$this->createdBy] : [];
    }

    /**
     * @return \Concrete\Core\Entity\Notification\UserSignupNotification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }
}
