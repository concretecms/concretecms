<?php
namespace Concrete\Core\Area\Layout\Preset\Formatter;

use Concrete\Core\Area\Layout\Layout;

class UserFormatter implements FormatterInterface
{
    protected $layout;

    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    public function getPresetContainerHtmlObject()
    {
        return $this->layout->getFormatter()->getLayoutContainerHtmlObject();
    }
}
