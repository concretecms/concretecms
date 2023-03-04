<?php

namespace Concrete\Core\Announcement\Icon;

abstract class AbstractIcon implements IconInterface
{

    public function jsonSerialize()
    {
        return [
            'element' => $this->getElement(),
        ];
    }
}
