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
        $formatter = $preset->getFormatter();
        return $formatter->getPresetContainerHtmlObject();
    }



}