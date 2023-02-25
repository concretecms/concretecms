<?php

namespace Concrete\Core\Application\UserInterface\Welcome\ContentItem;

abstract class AbstractAction implements ActionInterface
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
