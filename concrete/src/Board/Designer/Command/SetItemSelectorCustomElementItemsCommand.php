<?php

namespace Concrete\Core\Board\Designer\Command;

use Concrete\Core\Entity\Board\Designer\CustomElement;
use Concrete\Core\Entity\Board\Item;

class SetItemSelectorCustomElementItemsCommand
{

    /**
     * @var CustomElement
     */
    protected $element;

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @param CustomElement $element
     * @param Item[] $items
     */
    public function __construct(CustomElement $element, $items)
    {
        $this->element = $element;
        $this->items = $items;
    }

    /**
     * @return CustomElement
     */
    public function getElement(): CustomElement
    {
        return $this->element;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }




}
