<?php

namespace Concrete\Core\StyleCustomizer;

use Concrete\Core\StyleCustomizer\Style\Style;

class StyleList implements \JsonSerializable
{
    /**
     * The list of the style sets.
     *
     * @var \Concrete\Core\StyleCustomizer\Set[]
     */
    protected $sets = [];

    /**
     * Add a new empty style set.
     *
     * @param string $name the name of the style set
     *
     * @return \Concrete\Core\StyleCustomizer\Set
     */
    public function addSet($name)
    {
        $s = new Set();
        $s->setName($name);
        $this->sets[] = $s;

        return $s;
    }

    /**
     * Get the list of the style sets.
     *
     * @return \Concrete\Core\StyleCustomizer\Set[]
     */
    public function getSets()
    {
        return $this->sets;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'sets' => $this->getSets(),
        ];
    }

    /**
     * Traverses all sets and retrieves all styles. Basically a shortcut to allow developers not to have to traverse
     * the sets themselves when working with the basic values in a style list.
     *
     * @return Style[]
     */
    public function getAllStyles(): iterable
    {
        foreach ($this->getSets() as $set) {
            foreach ($set->getStyles() as $style) {
                yield $style;
            }
        }
    }
}
