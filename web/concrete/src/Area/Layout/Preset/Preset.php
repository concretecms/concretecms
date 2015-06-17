<?php
namespace Concrete\Core\Area\Layout\Preset;

use HtmlObject\Element;

class Preset implements PresetInterface
{

    protected $name;

    public function __construct($name, $columns = array())
    {
        $this->name = $name;
        $this->columns = $columns;
    }

    public function addColumn(Element $column)
    {
        $this->columns[] = $column;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


}