<?php

namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Notification\View\UserDeactivatedListView;
use Concrete\Core\User\Event\DeactivateUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="UserDeactivatedNotifications"
 * )
 */
class UserDeactivatedNotification extends Notification
{

    /**
     * The user that is being deactivated
     *
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $userID;

    /**
     * The user doing the deactivating
     *
     * @ORM\Column(type="integer", nullable=true, options={"unsigned": true})
     */
    protected $actorID = null;

    /**
     * UserSignupNotification constructor.
     *
     * @param \Concrete\Core\User\Event\DeactivateUser $event
     */
    public function __construct(DeactivateUser $event)
    {
        $this->userID = $event->getUserEntity()->getUserID();

        $actor = $event->getActorEntity();
        if ($actor) {
            $this->actorID = $event->getActorEntity()->getUserID();
        }

        parent::__construct($event);
    }

    public function getListView()
    {
        /** @todo Replace this with something that enables autowiring */
        return new UserDeactivatedListView($this);
    }

    /**
     * Get the deactivated user id
     *
     * @return int
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * Get the user id of the user that triggered deactivation
     *
     * @return int
     */
    public function getActorID()
    {
        return $this->actorID;
    }
}
