<?php

namespace Concrete\Core\Attribute;

use HtmlObject\Element;

/**
 * A legacy class for those attribute type that don't implement their own formatter
 * Class StandardTypeFormatter
 * @package Concrete\Core\Attribute
 */
class LegacyIconFormatter implements IconFormatterInterface
{

    protected $controller;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    public function getListIconElement()
    {
        $img = new Element('img');
        $img->addClass('ccm-attribute-icon')
            ->src($this->controller->getAttributeType()->getAttributeTypeIconSRC())
            ->width(16)
            ->height(16);
        return $img;
    }

}