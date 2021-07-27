<?php

namespace Concrete\Core\Attribute\Command;

class SaveAttributesCommandHandler
{

    public function __invoke(SaveAttributesCommand $command)
    {
        $keys = $command->getAttributeKeys();
        foreach($keys as $key) {
            $controller = $key->getController();
            $value = $controller->createAttributeValueFromRequest();
            $command->getObject()->setAttribute($key, $value);
        }

    }

}