<?php

namespace Concrete\Core\Announcement\Component;

abstract class AbstractComponent implements ComponentInterface
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
