<?
namespace Concrete\Core\Search;
class Result {

	protected $summary;
	protected $listColumns;
	protected $list;
	protected $baseURL;
	protected $pagination;

	protected $items;
	protected $fields;
	protected $columns;

	public function getItemListObject() {return $this->list;}

	public function setBaseURL($url) {
		$this->baseURL = $url;
	}

	public function getBaseURL() {
		return $this->baseURL;
	}

	public function __construct(DatabaseItemListColumnSet $columns, ItemList $il, $url, $fields = array()) {
		$this->summary = $il->getSummary();
		$this->listColumns = $columns;
		$this->list = $il;
		$this->baseURL = $url;
		$this->fields = $fields;
	}

	public function getItems() {
		if (!isset($this->items)) {
			$this->items = array();
			$items = $this->list->getPage();
			foreach($items as $item) {
				$node = $this->getItemDetails($item);
				$this->items[] = $node;
			}
		}
		return $this->items;
	}

	public function getColumns() {
		if (!isset($this->columns)) {
			$this->columns = array();
			foreach($this->listColumns->getColumns() as $column) {
				$node = $this->getColumnDetails($column);
				$this->columns[] = $node;
			}
		}
		return $this->columns;
	}

	public function getColumnDetails($column) {
		$node = new SearchResultColumn($this, $column);
		return $node;
	}

	public function getItemDetails($item) {
		$node = new SearchResultItem($this, $this->listColumns, $item);
		return $node;
	}

	public function getJSONObject() {
		$obj = new stdClass;
		$obj->items = array();
		foreach($this->getItems() as $item) {
			$obj->items[] = $item;
		}
		foreach($this->getColumns() as $column) {
			$obj->columns[] = $column;
		}
		$obj->summary = $this->summary;
		$obj->pagination = $this->list->getPagination($this->getBaseURL())->getAsJSONObject();
		$obj->fields = $this->fields;
		return $obj;
	}


}
