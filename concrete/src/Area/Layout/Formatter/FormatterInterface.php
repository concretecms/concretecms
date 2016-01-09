<?php
namespace Concrete\Core\Area\Layout\Formatter;

use Concrete\Core\Area\Layout\Layout;

interface FormatterInterface
{

    public function __construct(Layout $layout);

    public function getLayoutContainerHtmlObject();

}