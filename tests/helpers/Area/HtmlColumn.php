<?php

namespace Concrete\TestHelpers\Area;

use Concrete\Core\Area\Layout\ColumnInterface;
use HtmlObject\Element;

class HtmlColumn implements ColumnInterface
{
    public function __construct($class)
    {
        $this->class = $class;
    }

    public function getColumnHtmlObject()
    {
        $column = new Element('div');
        $column->addClass($this->class);

        return $column;
    }

    public function getColumnHtmlObjectEditMode()
    {
        return $this->getColumnHtmlObject();
    }
}
