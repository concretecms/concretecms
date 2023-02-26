<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Type;

use Concrete\Core\Application\UserInterface\Welcome\Modal\Slide\Slide;
use Concrete\Core\Updater\Announcement\AnnouncementService;
use Concrete\Core\User\User;

class ShowUpdateInformationType extends Type
{

    /**
     * @var AnnouncementService
     */
    protected $announcementService;

    public function __construct(AnnouncementService $announcementService)
    {
        $this->announcementService = $announcementService;
    }

    public function showModal(User $user, array $modalDrivers): bool
    {

        $slides = $this->announcementService->getAnnouncementSlidesForUser($user);
        if (count($slides)) {
            return true;
        }
        return false;
    }

    public function markModalAsViewed(User $user)
    {
    }

    public function getSlides(User $user): array
    {
        $slides = $this->announcementService->getAnnouncementSlidesForUser($user);
        return $slides;
    }


}
