<?php

namespace Concrete\Core\Area\Layout\Formatter;

use Concrete\Core\Area\Layout\Layout;
use HtmlObject\Element;

class PresetFormatter implements FormatterInterface
{

    protected $layout;

    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    public function getLayoutContainerHtmlObject()
    {
        $preset = $this->layout->getPresetObject();
        if (is_object($preset)) {
            $formatter = $preset->getFormatter();
            return $formatter->getPresetContainerHtmlObject();
        }
        return new Element('div');
    }



}