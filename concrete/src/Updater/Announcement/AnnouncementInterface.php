<?php
namespace Concrete\Core\Updater\Announcement;

use Concrete\Core\Application\UserInterface\Welcome\Modal\Slide\SlideInterface;

interface AnnouncementInterface
{

    /**
     * @return SlideInterface[]
     */
    public function getSlides(): array;


}
