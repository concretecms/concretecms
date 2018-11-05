<?php

namespace Concrete\TestHelpers\Area;

use Concrete\Core\Area\Layout\Preset\Formatter\FormatterInterface;
use HtmlObject\Element;

class TestAreaLayoutPresetFormatter implements FormatterInterface
{
    public function getPresetContainerHtmlObject()
    {
        $column = new Element('div');
        $column->addClass('foo');

        return $column;
    }
}
