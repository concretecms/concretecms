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
     * The user that is being deactivated.
     *
     * @ORM\Column(type="integer", options={"unsigned": true})
     *
     * @var int
     */
    protected $userID;

    /**
     * The user doing the deactivating.
     *
     * @ORM\Column(type="integer", nullable=true, options={"unsigned": true})
     *
     * @var int|null
     */
    protected $actorID;

    public function __construct(DeactivateUser $event)
    {
        $this->userID = $event->getUserEntity()->getUserID();
        $actor = $event->getActorEntity();
        if ($actor) {
            $this->actorID = $event->getActorEntity()->getUserID();
        }
        parent::__construct($event);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Entity\Notification\Notification::getListView()
     */
    public function getListView()
    {
        /** @todo Replace this with something that enables autowiring */
        return new UserDeactivatedListView($this);
    }

    /**
     * Get the deactivated user id.
     *
     * @return int
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * Get the user id of the user that triggered deactivation, if available.
     *
     * @return int|null
     */
    public function getActorID()
    {
        return $this->actorID;
    }
}
