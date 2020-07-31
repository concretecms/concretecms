<?php

namespace Concrete\Core\Attribute\Command;

class ClearAttributesCommandHandler
{

    public function handle(ClearAttributesCommand $command)
    {
        $keys = $command->getAttributeKeys();
        foreach($keys as $key) {
            $command->getObject()->clearAttribute($key);
        }
    }

}