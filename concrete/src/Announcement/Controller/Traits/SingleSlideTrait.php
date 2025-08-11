<?php

namespace Concrete\Core\Announcement\Controller\Traits;

use Concrete\Core\Announcement\Slide\SlideInterface;
use Concrete\Core\User\User;

trait SingleSlideTrait
{

    abstract public function getSlide(User $user): SlideInterface;

    public function getSlides(User $user): array
    {
        return [$this->getSlide($user)];
    }
}
