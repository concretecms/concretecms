<?php
namespace Concrete\Core\Attribute;

use HtmlObject\Element;

/**
 * A legacy class for those attribute type that don't implement their own formatter
 * Class StandardTypeFormatter.
 *
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
        $env = \Environment::get();
        $type = $this->controller->getAttributeType();
        $url = $env->getURL(
            implode('/', array(DIRNAME_ATTRIBUTES . '/' . $type->getAttributeTypeHandle() . '/' . FILENAME_BLOCK_ICON)),
            $type->getPackageHandle()
        );

        $img = new Element('img');
        $img->addClass('ccm-attribute-icon')
            ->src($url)
            ->width(16)
            ->height(16);

        return $img;
    }
}
