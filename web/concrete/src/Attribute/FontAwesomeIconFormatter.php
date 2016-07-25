<?php
namespace Concrete\Core\Attribute;

use HtmlObject\Element;

class FontAwesomeIconFormatter implements IconFormatterInterface
{
    protected $icon;

    public function __construct($icon)
    {
        $this->icon = $icon;
    }

    public function getListIconElement()
    {
        $span = new Element('i');
        $span->addClass('ccm-attribute-icon fa fa-' . $this->icon);

        return $span;
    }
}
