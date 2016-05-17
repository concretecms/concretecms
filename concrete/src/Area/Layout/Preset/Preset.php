<?php
namespace Concrete\Core\Area\Layout\Preset;

use Concrete\Core\Area\Layout\ColumnInterface;
use Concrete\Core\Area\Layout\Preset\Formatter\FormatterInterface;

class Preset implements PresetInterface
{
    protected $name;
    protected $identifier;
    protected $formatter;

    public function __construct($identifier, $name, FormatterInterface $formatter, $columns = array())
    {
        $this->name = $name;
        $this->identifier = $identifier;
        $this->formatter = $formatter;
        foreach ($columns as $column) {
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

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public static function getByID($identifier)
    {
        $manager = \Core::make('manager/area_layout_preset_provider');

        return $manager->getPresetByIdentifier($identifier);
    }

    /**
     * @return \Concrete\Core\Area\Layout\Preset\Formatter\FormatterInterface
     */
    public function getFormatter()
    {
        return $this->formatter;
    }
}
