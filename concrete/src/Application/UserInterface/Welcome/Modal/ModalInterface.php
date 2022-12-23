<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Modal;

use Concrete\Core\Application\UserInterface\Welcome\Modal\Slide\SlideInterface;

interface ModalInterface extends \JsonSerializable
{

    /**
     * @return SlideInterface[]
     */
    public function getSlides(): array;


}
