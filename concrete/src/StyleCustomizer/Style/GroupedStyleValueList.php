<?php

namespace Concrete\Core\StyleCustomizer\Style;

class GroupedStyleValueList implements \JsonSerializable
{

    protected $sets = [];

    public function addSet(GroupedStyleValueListSet $set)
    {
        $this->sets[] = $set;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'sets' => $this->sets
        ];
    }

}
