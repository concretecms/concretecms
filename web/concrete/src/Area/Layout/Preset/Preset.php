<?php
namespace Concrete\Core\Area\Layout\Preset;

use Concrete\Core\Area\Layout\ColumnInterface;

class Preset implements PresetInterface
{

    protected $name;

    public function __construct($name, $columns = array())
    {
        $this->name = $name;
        foreach($columns as $column) {
            $this->addColumn($column);
        }
    }

    public function addColumn(ColumnInterface $column)
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