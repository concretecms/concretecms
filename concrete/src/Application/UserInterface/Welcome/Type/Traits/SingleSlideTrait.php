<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Type\Traits;

use Concrete\Core\Application\UserInterface\Welcome\Modal\Slide\SlideInterface;

trait SingleSlideTrait
{

    abstract public function getSlide(): SlideInterface;

    public function getSlides(): array
    {
        return [$this->getSlide()];
    }
}
