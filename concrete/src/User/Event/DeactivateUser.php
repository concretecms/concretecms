<?php

namespace Concrete\Core\User\Event;

use Carbon\Carbon;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\User\Event\UserInfo as UserInfoEvent;
use DateTime;

class DeactivateUser extends UserInfoEvent implements SubjectInterface
{

    /**
     * The datetime this event was created
     *
     * @var \DateTime
     */
    protected $created;

    /**
     * The user that is running the deactivate operation
     *
     * @var \Concrete\Core\Entity\User\User|null
     */
    protected $user;

    /**
     * The user that is running the deactivate operation
     *
     * @var \Concrete\Core\Entity\User\User|null
     */
    protected $actor;

    /**
     * DeactivateUser constructor.
     *
     * @param \Concrete\Core\Entity\User\User $user
     * @param \Concrete\Core\Entity\User\User|null $actorEntity
     * @param \DateTime|null $dateCreated
     */
    public function __construct(UserEntity $user, UserEntity $actorEntity = null, DateTime $dateCreated = null)
    {
        $this->user = $user;
        $this->actor = $actorEntity;
        $this->created = $dateCreated ?: Carbon::now('utc');
    }

    /**
     * Get the date of this notification
     *
     * @return \DateTime
     */
    public function getNotificationDate()
    {
        return $this->created;
    }

    /**
     * Get the users that should be excluded from notifications
     * Expected return value would be users involved in the creation of the notification, they may not need to be
     * notified.
     *
     * @return \Concrete\Core\Entity\User\User[]
     */
    public function getUsersToExcludeFromNotification()
    {
        return [$this->getUserEntity()];
    }

    /**
     * Pass through calls for the user info object to the associated user info object
     *
     * @return \Concrete\Core\User\UserInfo|null
     */
    public function getUserInfoObject()
    {
        return $this->user->getUserInfoObject();
    }

    /**
     * Get the user that is being deactivated
     *
     * @return \Concrete\Core\Entity\User\User
     */
    public function getUserEntity()
    {
        return $this->user;
    }

    /**
     * Get the user that is running the deactivation
     *
     * @return \Concrete\Core\Entity\User\User|null
     */
    public function getActorEntity()
    {
        return $this->actor;
    }

    /**
     * Factory method for creating new User event objects
     *
     * @param \Concrete\Core\Entity\User\User $userEntity The user being deactivated
     * @param \Concrete\Core\Entity\User\User|null $actorEntity The user running the deactivate action
     * @param \DateTime|null $dateCreated
     *
     * @return \Concrete\Core\User\Event\DeactivateUser
     */
    public static function create(UserEntity $userEntity, UserEntity $actorEntity = null, DateTime $dateCreated = null)
    {
        return new self($userEntity, $actorEntity, $dateCreated);
    }
}
