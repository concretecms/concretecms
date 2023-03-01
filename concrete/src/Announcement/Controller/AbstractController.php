<?php

namespace Concrete\Core\Announcement\Controller;

use Concrete\Core\Announcement\Announcement\Announcement;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Entity\Announcement\Announcement as AnnouncementEntity;

abstract class AbstractController implements ControllerInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    public function createAnnouncementComponent(AnnouncementEntity $announcement, array $slides)
    {
        return new Announcement($announcement->getHandle(), $slides);
    }



}
