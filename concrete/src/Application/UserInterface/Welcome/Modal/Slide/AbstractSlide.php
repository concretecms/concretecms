<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Modal\Slide;

abstract class AbstractSlide implements SlideInterface
{

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'component' => $this->getComponent(),
            'componentProps' => $this->getComponentProps(),
        ];
    }


}
