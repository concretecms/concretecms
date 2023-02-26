<?php

namespace Concrete\Core\Application\UserInterface\Welcome\ContentItem\Icon;

abstract class AbstractIcon implements IconInterface
{

    public function jsonSerialize()
    {
        return [
            'element' => $this->getElement(),
        ];
    }
}
