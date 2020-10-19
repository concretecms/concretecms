<?php
namespace Concrete\Core\File\Component\Chooser;

trait OptionSerializableTrait
{

    public function jsonSerialize()
    {
        return [
            'key' => $this->getComponentKey(),
            'title' => $this->getTitle(),
        ];
    }
    
}