<?php

namespace Concrete\Core\Attribute\Command;

class SaveAttributesCommandHandler
{

    public function __invoke(SaveAttributesCommand $command)
    {
        $keys = $command->getAttributeKeys();
        foreach($keys as $key) {
            $controller = $key->getController();
            if (method_exists($controller, 'setAttributeObject')) {
                $controller->setAttributeObject($command->getObject());
            }
            $value = $controller->createAttributeValueFromRequest();
            $command->getObject()->setAttribute($key, $value);
        }

    }

}