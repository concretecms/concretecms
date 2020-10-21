<?php
namespace Concrete\Core\File\Component\Chooser;

trait OptionSerializableTrait
{

    public function getId() {
        return $this->getComponentKey();
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'componentKey' => $this->getComponentKey(),
            'title' => $this->getTitle(),
        ];
    }
    
}