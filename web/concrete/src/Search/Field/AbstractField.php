<?php
namespace Concrete\Core\Search\Field;

abstract class AbstractField implements FieldInterface
{

    public function renderSearchField()
    {
        return '';
    }

    public function jsonSerialize()
    {
        return [
            'key' => $this->getKey(),
            'label' => $this->getDisplayName(),
            'element' => $this->renderSearchField()
        ];
    }
}
