<?php
namespace Concrete\Core\Search\Result;
use \Concrete\Core\Search\Column\Column as DatabaseItemListColumn;
use Core;
class Column {

	public $isColumnSortable;
	public $key;
	public $title;
	public $className;
	public $sortURL;

	public function isColumnSortable() {
		return $this->isColumnSortable;
	}

	public function setColumnSortable($sortable) {
		$this->isColumnSortable = $sortable;
	}

	public function setColumnKey($key) {
		$this->key = $key;
	}

	public function getColumnKey() {
		return $this->key;
	}

	public function setColumnTitle($title) {
		$this->title = $title;
	}

	public function getColumnTitle() {
		return $this->title;
	}

	public function setColumnStyleClass($class) {
		$this->className = $class;
	}

	public function getColumnStyleClass() {
		return $this->className;
	}

	public function setColumnSortURL($url) {
		$this->sortURL = $url;
	}

	public function getColumnSortURL() {
		return $this->sortURL;
	}

	public function __construct(Result $result, \Concrete\Core\Search\Column\Column $column = null) {
		if ($column instanceof DatabaseItemListColumn) {
			$this->isColumnSortable = $column->isColumnSortable();
			$this->key = $column->getColumnKey();
			$this->title = $column->getColumnName();
			$this->className = $column->getSortClassName($result);
            $this->sortURL = $column->getSortURL($result);
		}
	}

}
