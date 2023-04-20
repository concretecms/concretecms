<?php

namespace Concrete\Core\Announcement\Item;

abstract class AbstractStandardItem implements ItemInterface
{

    abstract public function getTitle(): string;

    abstract public function getDescription(): string;

    abstract public function getActions(): array;

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'actions' => $this->getActions(),
        ];
    }
}
