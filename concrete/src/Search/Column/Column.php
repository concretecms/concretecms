<?php
namespace Concrete\Core\Search\Column;

use Concrete\Core\Search\Result\Result;

class Column implements ColumnInterface
{
    /** These properties are to be treated as protected. Use the set and get methods instead */
    /** @deprecated */
    public $columnKey;

    /** @deprecated */
    public $columnName;

    /** @deprecated */
    public $sortDirection = 'asc';

    /** @deprecated */
    public $isSortable;

    /** @deprecated */
    public $callback;

    public function getColumnValue($obj)
    {
        if (is_array($this->getColumnCallback())) {
            return call_user_func($this->getColumnCallback(), $obj);
        }

        return call_user_func([$obj, $this->getColumnCallback()]);
    }

    public function getColumnKey()
    {
        return $this->columnKey;
    }

    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     * @deprecated
     */
    public function getColumnDefaultSortDirection()
    {
        return $this->sortDirection;
    }

    public function isColumnSortable()
    {
        return $this->isSortable;
    }

    public function getColumnCallback()
    {
        return $this->callback;
    }

    /**
     * @deprecated
     */
    public function setColumnDefaultSortDirection($dir)
    {
        $this->setColumnSortDirection($dir);
    }

    public function getSortClassName(Result $result)
    {
        $il = $result->getItemListObject();

        return $il->getSortClassName($this->getColumnKey());
    }

    public function getColumnSortDirection()
    {
        return $this->sanitizeSortDirection($this->sortDirection);
    }

    public function setColumnSortDirection($sortDirection)
    {
        return $this->sortDirection = $this->sanitizeSortDirection($sortDirection);
    }

    /**
     * Normalize a sort direction to "asc" or "desc".
     *
     * @param $direction
     *
     * @return string
     */
    private function sanitizeSortDirection($direction)
    {
        return strtolower(trim($direction)) === 'asc' ? 'asc' : 'desc';
    }

    public function getSortURL(Result $result)
    {
        $il = $result->getItemListObject();
        $dir = $this->getColumnSortDirection();

        return $il->getSortURL($this->getColumnKey(), $dir, $result->getBaseURL());
    }

    public function __construct($key = null, $name = null, $callback = null, $isSortable = true, $sort = 'asc')
    {
        $this->columnKey = $key;
        $this->columnName = $name;
        $this->isSortable = $isSortable;
        $this->callback = $callback;
        $this->sortDirection = $this->sanitizeSortDirection($sort);
    }

    public function jsonSerialize()
    {
        return [
            'columnKey' => $this->getColumnKey(),
            'isSortable' => $this->isColumnSortable(),
            'sortDirection' => $this->getColumnSortDirection(),
        ];
    }
}
