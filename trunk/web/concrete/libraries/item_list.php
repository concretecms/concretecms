<?
/**
*
* A base class for working with lists of objects. Implements things like pagination & offset, getting the total, and iteration
* @package Utilities
*/
defined('C5_EXECUTE') or die(_("Access Denied."));

class ItemList {

	private $total = -1; // initial state == unknown
	private $itemsPerPage = 0;
	private $currentPage = 1;
	private $start = 0;
	private $sortBy;
	private $sortByDirection;
	private $query = '';
	private $debug = false;
	private $filters = array();
	
	public function setItemsPerPage($num) {
		$this->itemsPerPage = $num;
	}
	
	/** 
	 * Returns the total number of items found by this list
	 */
	public function getTotal() {
		if ($this->total == -1) {
			$db = Loader::db();
			$arr = $this->executeBase(); // returns an associated array of query/placeholder values
			$r = $db->Execute($arr[0], $arr[1]);
			$this->total = $r->NumRows();
		}
		
		return $this->total;
	}
	
	/** 
	 * Returns an array of object by "page"
	 */
	public function getPage($page = 1) {
		$offset = 0;
		if ($page > 1) {
			$offset = $this->itemsPerPage * ($page - 1);
		}
		return $this->execute($this->itemsPerPage, $offset);
	}
	
	public function debug($dbg = true) {
		$this->debug = $dbg;
	}
	
	/** 
	 * Displays summary text about a list
	 */
	public function displaySummary() {
		if ($this->total < 1) {
			return false;
		}
		
		$summary = $this->getSummary();

		$html = '<div class="ccm-paging-top">' . t('Viewing <b>%s</b> to <b>%s</b> (<b>%s</b> Total)', $currentRangeStart, "<span id=\"pagingPageResults\">" . $currentRangeEnd . "</span>", "<span id=\"pagingTotalResults\">" . $total . "</span>") . '</div>';
		print $html;
	}
	
	private function executeBase() {
		$v = array();		
		$q = $this->query . ' where 1=1 ';
		foreach($this->filters as $f) {
			$column = $f[0];
			$comp = $f[2];
			$value = $f[1];
			// if there is NO column, then we have a free text filter that we just add on
			if ($column == false || $column == '') {
				$q .= 'and ' . $f[1];
			} else {
				if (is_array($value)) {
					switch($comp) {
						case '=':
							$comp = 'in';
							break;
						case '!=':
							$comp = 'not in';
							break;
					}
					$q .= 'and ' . $column . ' ' . $comp . ' (';
					for ($i = 0; $i < count($value); $i++) {
						if ($i > 0) {
							$q .= ',';
						}
						$q .= '?';
						$v[] = $value[$i];
					}
					$q .= ') ';			
				} else { 
					$q .= 'and ' . $column . ' ' . $comp . ' ? ';
					$v[] = $value;
				}
			}
		}
		
		return array($q, $v);
	}
	
	/** 
	 * Returns an array of whatever objects extends this class (e.g. PageList returns a list of pages).
	 */
	public function execute($itemsToGet = 0, $offset = 0) {
		$arr = $this->executeBase(); // returns an associated array of query/placeholder values
		$q = $arr[0];
		$v = $arr[1];
		// handle order by 
		if ($this->sortBy != '') {
			$q .= 'order by ' . $this->sortBy . ' ' . $this->sortByDirection . ' ';
		}
		if ($itemsPerPage > 0) {
			$q .= 'limit ' . $offset . ',' . $itemsToGet . ' ';
		}
		
		$db = Loader::db();
		if ($this->debug) { 
			$db->setDebug(true);
		}
		$resp = $db->GetAll($q, $v);
		if ($this->debug) { 
			$db->setDebug(false);
		}
		
		return $resp;
	}
	
	/** 
	 * Adds a filter to this item list
	 */
	public function filter($column, $value, $comparison = '=') {
		$this->filters[] = array($column, $value, $comparison);
	}
	
	/** 
	 * Sets up a column to sort by
	 */
	public function sortBy($column, $direction = 'asc') {
		$this->sortBy = $column;
		$this->sortByDirection = $direction;
	}
	
	protected function setQuery($query) {
		$this->query = $query . ' ';
	}
	
	protected function addToQuery($query) {
		$this->query .= $query . ' ';
	}

}