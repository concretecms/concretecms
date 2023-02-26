<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Type\Traits;

use Concrete\Core\Application\UserInterface\Welcome\Modal\Slide\SlideInterface;
use Concrete\Core\User\User;

trait SingleSlideTrait
{

    abstract public function getSlide(User $user): SlideInterface;

    public function getSlides(User $user): array
    {
        return [$this->getSlide($user)];
    }
}
