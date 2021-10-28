<?php
namespace Concrete\Core\Application\UserInterface\Icon;

use HtmlObject\Element;

class BasicIconFormatter implements IconFormatterInterface
{
    protected $icon;

    public function __construct($icon)
    {
        $this->icon = $icon;
    }

    public function getListIconElement()
    {
        $element = new Element('i');
        $element->addClass($this->icon);
        return $element;
    }
}
