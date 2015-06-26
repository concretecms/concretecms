<?php

namespace Concrete\Core\Area\Layout\Formatter;

use Concrete\Core\Area\Layout\Layout;
use HtmlObject\Element;

class CustomFormatter implements FormatterInterface
{

    protected $layout;

    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    public function getLayoutContainerHtmlObject()
    {
        $element = new Element('div');
        $element->addClass('ccm-layout-column-wrapper')
            ->id('ccm-layout-column-wrapper-' . $this->layout->getAreaLayoutID());
        return $element;
    }



}