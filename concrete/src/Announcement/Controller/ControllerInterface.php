<?php

namespace Concrete\Core\Announcement\Controller;

use Concrete\Core\Announcement\Slide\SlideInterface;
use Concrete\Core\Entity\Announcement\Announcement;
use Concrete\Core\User\User;

interface ControllerInterface
{

    /**
     * Allows for additional filtering of announcements
     *
     * @param User $user
     * @param array $announcements
     * @return bool
     */
    public function shouldDisplayAnnouncementToUser(User $user): bool;

    public function onViewAnnouncement(User $user);

    /**
     * @param Announcement $announcement
     * @param SlideInterface $slides
     * @return mixed
     */
    public function createAnnouncementComponent(Announcement $announcement, array $slides);

    /**
     * @return SlideInterface[]
     */
    public function getSlides(User $user): array;

}
