<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu\Item;

use HtmlObject\Element;
use HtmlObject\Link;

class LinkItem extends AbstractItem
{

    protected $link;
    protected $value;
    protected $attributes = array();

    /**
     * LinkItem constructor.
     * @param $link
     * @param $value
     * @param array $attributes
     */
    public function __construct($link, $value, $attributes = array())
    {
        $this->link = $link;
        $this->value = $value;
        $this->attributes = $attributes;
    }

    public function getItemElement()
    {
        $element = new Element('li');
        $link = new Link($this->link, $this->value, $this->attributes);
        $element->appendChild($link);
        return $element;
    }

}