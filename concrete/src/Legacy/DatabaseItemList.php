<?php
namespace Concrete\Core\Legacy;

class DatabaseItemList extends ItemList
{
    protected $query = '';
    protected $userQuery = '';
    protected $debug = false;
    protected $filters = array();
    protected $sortByString = '';
    protected $groupByString = '';
    protected $havingString = '';
    protected $autoSortColumns = array();
    protected $userPostQuery = '';
    protected $attributeClass = '';

    public function getTotal()
    {
        if ($this->total == -1) {
            $db = Loader::db();
            $arr = $this->executeBase(); // returns an associated array of query/placeholder values
            $r = $db->Execute($arr);
            $this->total = $r->NumRows();
        }

        return $this->total;
    }

    public function debug($dbg = true)
    {
        $this->debug = $dbg;
    }

    protected function setQuery($query)
    {
        $this->query = $query . ' ';
    }

    protected function getQuery()
    {
        return $this->query;
    }

    public function addToQuery($query)
    {
        $this->userQuery .= $query . ' ';
    }

    protected function setupAutoSort()
    {
        if (count($this->autoSortColumns) > 0) {
            $req = $this->getSearchRequest();
            if (
                isset($req[$this->queryStringSortVariable])
                &&
                in_array($req[$this->queryStringSortVariable], $this->autoSortColumns)
            ) {
                $this->sortBy($req[$this->queryStringSortVariable], $req[$this->queryStringSortDirectionVariable]);
            }
        }
    }

    protected function executeBase()
    {
        $db = Loader::db();
        $q = $this->query . $this->userQuery . ' where 1=1 ';
        foreach ($this->filters as $f) {
            $column = $f[0];
            $comp = $f[2];
            $value = $f[1];
            // if there is NO column, then we have a free text filter that we just add on
            if ($column == false || $column == '') {
                $q .= 'and ' . $f[1] . ' ';
            } else {
                if (is_array($value)) {
                    if (count($value) > 0) {
                        switch ($comp) {
                            case '=':
                                $comp = 'in';
                                break;
                            case '!=':
                                $comp = 'not in';
                                break;
                        }
                        $q .= 'and ' . $column . ' ' . $comp . ' (';
                        for ($i = 0; $i < count($value); ++$i) {
                            if ($i > 0) {
                                $q .= ',';
                            }
                            $q .= $db->quote($value[$i]);
                        }
                        $q .= ') ';
                    } else {
                        $q .= 'and 1 = 2 ';
                    }
                } else {
                    $comp = (is_null($value) && stripos($comp, 'is') === false) ? (($comp == '!=' || $comp == '<>') ? 'IS NOT' : 'IS') : $comp;
                    $q .= 'and ' . $column . ' ' . $comp . ' ' . $db->quote($value) . ' ';
                }
            }
        }

        if ($this->userPostQuery != '') {
            $q .= ' ' . $this->userPostQuery . ' ';
        }

        if ($this->groupByString != '') {
            $q .= 'group by ' . $this->groupByString . ' ';
        }

        if ($this->havingString != '') {
            $q .= 'having ' . $this->havingString . ' ';
        }

        return $q;
    }

    protected function setupSortByString()
    {
        if ($this->sortByString == '' && $this->sortBy != '') {
            $this->sortByString = $this->sortBy . ' ' . $this->sortByDirection;
        }
    }

    protected function setupAttributeSort()
    {
        if ($this->attributeClass && is_callable(array($this->attributeClass, 'getList'))) {
            $l = call_user_func(array($this->attributeClass, 'getList'));
            foreach ($l as $ak) {
                $this->autoSortColumns[] = 'ak_' . $ak->getAttributeKeyHandle();
            }
            if ($this->sortBy != '' && in_array('ak_' . $this->sortBy, $this->autoSortColumns)) {
                $this->sortBy = 'ak_' . $this->sortBy;
            }
        }
    }

    /**
     * Returns an array of whatever objects extends this class (e.g. PageList returns a list of pages).
     */
    public function get($itemsToGet = 0, $offset = 0)
    {
        $q = $this->executeBase();
        // handle order by
        $this->setupAttributeSort();
        $this->setupAutoSort();
        $this->setupSortByString();

        if ($this->sortByString != '') {
            $q .= 'order by ' . $this->sortByString . ' ';
        }
        if ($this->itemsPerPage > 0 && (intval($itemsToGet) || intval($offset))) {
            $q .= 'limit ' . $offset . ',' . $itemsToGet . ' ';
        }

        $db = Loader::db();
        $resp = $db->GetAll($q);

        $this->start = $offset;

        return $resp;
    }

    /**
     * Adds a filter to this item list.
     */
    public function filter($column, $value, $comparison = '=')
    {
        $foundFilterIndex = -1;
        if ($column) {
            foreach ($this->filters as $key => $info) {
                if ($info[0] == $column) {
                    $foundFilterIndex = $key;
                    break;
                }
            }
        }

        if ($foundFilterIndex > -1) {
            $this->filters[$foundFilterIndex] = array($column, $value, $comparison);
        } else {
            $this->filters[] = array($column, $value, $comparison);
        }
    }

    public function getSearchResultsClass($field)
    {
        if ($field instanceof AttributeKey) {
            $field = 'ak_' . $field->getAttributeKeyHandle();
        }

        return parent::getSearchResultsClass($field);
    }

    public function sortBy($key, $dir = 'asc')
    {
        if ($key instanceof AttributeKey) {
            $key = 'ak_' . $key->getAttributeKeyHandle();
        }
        parent::sortBy($key, $dir);
    }

    public function groupBy($key)
    {
        if ($key instanceof AttributeKey) {
            $key = 'ak_' . $key->getAttributeKeyHandle();
        }
        $this->groupByString = $key;
    }

    public function having($column, $value, $comparison = '=')
    {
        if ($column == false) {
            $this->havingString = $value;
        } else {
            $this->havingString = $column . ' ' . $comparison . ' ' . $value;
        }
    }

    public function getSortByURL($column, $dir = 'asc', $baseURL = false, $additionalVars = array())
    {
        if ($column instanceof AttributeKey) {
            $column = 'ak_' . $column->getAttributeKeyHandle();
        }

        return parent::getSortByURL($column, $dir, $baseURL, $additionalVars);
    }

    protected function setupAttributeFilters($join)
    {
        $i = 1;
        $this->addToQuery($join);
        foreach ($this->attributeFilters as $caf) {
            $this->filter($caf[0], $caf[1], $caf[2]);
        }
    }

    public function filterByAttribute($column, $value, $comparison = '=')
    {
        if (is_array($column)) {
            $column = $column[key($column)] . '_' . key($column);
        }
        $this->attributeFilters[] = array('ak_' . $column, $value, $comparison);
    }
}
