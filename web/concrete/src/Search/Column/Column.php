<?php
namespace Concrete\Core\Search\Column;
use Loader;
use Concrete\Core\Search\Result\Result;
class Column {

	public function getColumnValue($obj) {
		if (is_array($this->callback)) {
			return call_user_func($this->callback, $obj);
		} else {
			return call_user_func(array($obj, $this->callback));
		}
	}

	public function getColumnKey() {return $this->columnKey;}
	public function getColumnName() {return $this->columnName;}
	public function getColumnDefaultSortDirection() {return $this->defaultSortDirection;}
	public function isColumnSortable() {return $this->isSortable;}
	public function getColumnCallback() {return $this->callback;}
	public function setColumnDefaultSortDirection($dir) {$this->defaultSortDirection = $dir;}
    public function getSortClassName(Result $result)
    {
        $class = '';
        $il = $result->getItemListObject();
        if ($il->isActiveSortColumn($this->getColumnKey())) {
            $class = 'ccm-results-list-active-sort-';
            if ($il->getActiveSortDirection() == 'desc') {
                $class .= 'desc';
            } else {
                $class .= 'asc';
            }
        }
        return $class;
    }

    public function getSortURL(Result $result)
    {
        $uh = Loader::helper('url');
        $il = $result->getItemListObject();
        $dir = $this->getColumnDefaultSortDirection();
        if ($il->isActiveSortColumn($this->getColumnKey()) && $il->getActiveSortDirection() == $dir) {
            $dir = ($dir == 'asc') ? 'desc' : 'asc';
        }

        $args = array(
            $il->getQuerySortColumnParameter() => $this->getColumnKey(),
            $il->getQuerySortDirectionParameter() => $dir
        );

        $url = $uh->setVariable($args, false, $result->getBaseURL());
        return strip_tags($url);
    }

    public function __construct($key, $name, $callback, $isSortable = true, $defaultSort = 'asc') {
		$this->columnKey = $key;
		$this->columnName = $name;
		$this->isSortable = $isSortable;
		$this->callback = $callback;
		$this->defaultSortDirection = $defaultSort;
	}
}
