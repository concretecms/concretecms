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

    public function removeColumnByKey($key)
    {
        foreach($this->columns as $i => $column) {
            if ($key == $column->getColumnKey()) {
                unset($this->columns[$i]);
            }
        }
    }

    public function __sleep()
    {
        return array('columns', 'defaultSortColumn');
    }


    public function __wakeup()
    {
        $i = 0;
        foreach ($this->columns as $col) {
            if (!$col) {
                unset($this->columns[$i]); // Somehow a null column was saved in the result set.
            }
            
            if ($col instanceof AttributeKeyColumn) {
                $ak = $this->getAttributeKeyColumn(substr($col->getColumnKey(), 3));
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

    /**
     * @param string $akHandle
     *
     * @return AttributeKeyColumn|null
     */
    public function getAttributeKeyColumn($akHandle)
    {
        $result = null;
        $ak = call_user_func(array($this->attributeClass, 'getByHandle'), $akHandle);
        if ($ak !== null) {
            $result = new AttributeKeyColumn($ak);
        }
        return $result;
    }

    public function getColumnByKey($key)
    {
        if (substr($key, 0, 3) == 'ak_') {
            return $this->getAttributeKeyColumn(substr($key, 3));
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
            if ($col instanceof ColumnInterface) {
                if ($_col->getColumnKey() == $col->getColumnKey()) {
                    return true;
                }
            } elseif (is_a($col, '\Concrete\Core\Attribute\AttributeKeyInterface')) {
                if ($_col->getColumnKey() == 'ak_' . $col->getAttributeKeyHandle()) {
                    return true;
                }
            }
        }

        return false;
    }
}
