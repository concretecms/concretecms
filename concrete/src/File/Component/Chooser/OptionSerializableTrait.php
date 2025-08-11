<?php
namespace Concrete\Core\File\Component\Chooser;

trait OptionSerializableTrait
{

    public function getId() {
        return $this->getComponentKey();
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'componentKey' => $this->getComponentKey(),
            'title' => $this->getTitle(),
        ];
    }
    
}