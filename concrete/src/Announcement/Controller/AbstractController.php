<?php

namespace Concrete\Core\Announcement\Controller;

use Concrete\Core\Announcement\Announcement\Announcement;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Entity\Announcement\Announcement as AnnouncementEntity;
use Concrete\Core\User\User;

abstract class AbstractController implements ControllerInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    public function shouldDisplayAnnouncementToUser(User $user): bool
    {
        return true;
    }

    public function onViewAnnouncement(User $user)
    {
        // Nothing here - but controllers can subclass.
    }

    public function createAnnouncementComponent(AnnouncementEntity $announcement, array $slides)
    {
        return new Announcement($announcement->getHandle(), $slides);
    }



}
