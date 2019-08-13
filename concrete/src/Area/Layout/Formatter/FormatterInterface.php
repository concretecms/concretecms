<?php
namespace Concrete\Core\Area\Layout\Formatter;

use Concrete\Core\Area\Layout\Layout;

/**
 * @since 5.7.5
 */
interface FormatterInterface
{
    public function __construct(Layout $layout);

    public function getLayoutContainerHtmlObject();
}
