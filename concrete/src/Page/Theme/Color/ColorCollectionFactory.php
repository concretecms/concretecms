<?php

namespace Concrete\Core\Page\Theme\Color;

class ColorCollectionFactory
{

    public function createFromArray($records): ColorCollection
    {
        $collection = new ColorCollection();
        foreach ($records as $variable => $name) {
            $collection->add(new Color($variable, $name));
        }
        return $collection;
    }
}
