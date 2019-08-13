<?php
namespace Concrete\Core\Area\Layout\Preset\Formatter;

use Concrete\Core\Area\Layout\Layout;

/**
 * @since 5.7.5
 */
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
