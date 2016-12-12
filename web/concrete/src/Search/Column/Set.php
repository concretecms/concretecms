<?php
namespace Concrete\Core\Search\Column;

class Set
{
    protected $columns = array();
    protected $defaultSortColumn;

    public function addColumn($col)
    {
        $this->columns[] = $col;
    }

    public function __wakeup()
    {
        $i = 0;
        foreach ($this->columns as $col) {
            if ($col instanceof AttributeKeyColumn) {
                $ak = call_user_func(array($this->attributeClass, 'getByHandle'), substr($col->getColumnKey(), 3));
                if (!is_object($ak)) {
                    unset($this->columns[$i]);
                }
            }
            ++$i;
        }
    }

    public function getSortableColumns()
    {
        $tmp = array();
        $columns = $this->getColumns();
        foreach ($columns as $col) {
            if ($col->isColumnSortable()) {
                $tmp[] = $col;
            }
        }

        return $tmp;
    }

    public function setDefaultSortColumn(Column $col, $direction = false)
    {
        if ($direction != false) {
            $col->setColumnDefaultSortDirection($direction);
        }
        $this->defaultSortColumn = $col;
    }

    public function getDefaultSortColumn()
    {
        return $this->defaultSortColumn;
    }

    public function getColumnByKey($key)
    {
        if (substr($key, 0, 3) == 'ak_') {
            $ak = call_user_func(array($this->attributeClass, 'getByHandle'), substr($key, 3));
            $col = new AttributeKeyColumn($ak);

            return $col;
        } else {
            foreach ($this->columns as $col) {
                if ($col->getColumnKey() == $key) {
                    return $col;
                }
            }
        }
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function contains($col)
    {
        foreach ($this->columns as $_col) {
            if ($col instanceof Column) {
                if ($_col->getColumnKey() == $col->getColumnKey()) {
                    return true;
                }
            } elseif (is_a($col, '\Concrete\Core\Attribute\Key\Key')) {
                if ($_col->getColumnKey() == 'ak_' . $col->getAttributeKeyHandle()) {
                    return true;
                }
            }
        }

        return false;
    }
}
