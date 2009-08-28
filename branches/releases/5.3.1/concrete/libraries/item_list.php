<?php 
/**
*
* @package Utilities
*/
defined('C5_EXECUTE') or die(_("Access Denied."));

class DatabaseItemList extends ItemList {

	private $query = '';
	private $userQuery = '';
	private $debug = false;
	private $filters = array();
	protected $sortByString = '';
	protected $autoSortColumns = array();
	protected $userPostQuery = '';
	
	public function getTotal() {
		if ($this->total == -1) {
			$db = Loader::db();
			$arr = $this->executeBase(); // returns an associated array of query/placeholder values
			$r = $db->Execute($arr[0], $arr[1]);
			$this->total = $r->NumRows();
		}		
		return $this->total;
	}


	public function debug($dbg = true) {
		$this->debug = $dbg;
	}
	
	protected function setQuery($query) {
		$this->query = $query . ' ';
	}
	
	protected function getQuery() {
		return $this->query;
	}
	
	protected function addToQuery($query) {
		$this->userQuery .= $query . ' ';
	}

	private function setupAutoSort() {
		if (count($this->autoSortColumns) > 0) {
			if (in_array($_REQUEST[$this->queryStringSortVariable], $this->autoSortColumns)) {
				$this->sortBy($_REQUEST[$this->queryStringSortVariable], $_REQUEST[$this->queryStringSortDirectionVariable]);
			}
		}
	}
	
	private function executeBase() {
		$v = array();		
		$q = $this->query . $this->userQuery . ' where 1=1 ';
		foreach($this->filters as $f) {
			$column = $f[0];
			$comp = $f[2];
			$value = $f[1];
			// if there is NO column, then we have a free text filter that we just add on
			if ($column == false || $column == '') {
				$q .= 'and ' . $f[1] . ' ';
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
		
		if ($this->userPostQuery != '') {
			$q .= ' ' . $this->userPostQuery . ' ';
		}
		
		return array($q, $v);
	}
	
	protected function setupSortByString() {
		if ($this->sortByString == '' && $this->sortBy != '') {
			$this->sortByString = $this->sortBy . ' ' . $this->sortByDirection;
		}
	}
	
	/** 
	 * Returns an array of whatever objects extends this class (e.g. PageList returns a list of pages).
	 */
	public function get($itemsToGet = 0, $offset = 0) {
		$arr = $this->executeBase(); // returns an associated array of query/placeholder values
		$q = $arr[0];
		$v = $arr[1];
		// handle order by
		$this->setupAutoSort();
		$this->setupSortByString();
		if ($this->sortByString != '') {
			$q .= 'order by ' . $this->sortByString . ' ';
		}
		if ($this->itemsPerPage > 0) {
			$q .= 'limit ' . $offset . ',' . $itemsToGet . ' ';
		}
		
		$db = Loader::db();
		if ($this->debug) { 
			$db->setDebug(true);
		}
		//echo $q.'<br>'; 
		$resp = $db->GetAll($q, $v);
		if ($this->debug) { 
			$db->setDebug(false);
		}
		
		$this->start = $offset;
		return $resp;
	}
	
	/** 
	 * Adds a filter to this item list
	 */
	public function filter($column, $value, $comparison = '=') {
		$this->filters[] = array($column, $value, $comparison);
	}
	
	

}

/** 
 * A base class for working with lists of objects. Implements things like pagination & offset, getting the total, and iteration
 * @package Utilities
 */
 
class ItemList {

	protected $total = -1; // initial state == unknown
	protected $itemsPerPage = 20;
	protected $currentPage = false;
	protected $start = 0;
	protected $sortBy;
	protected $sortByDirection;
	protected $queryStringPagingVariable = 'ccm_paging_p';
	protected $queryStringSortVariable = 'ccm_order_by';
	protected $queryStringSortDirectionVariable = 'ccm_order_dir';
	
	private $items = array();
	
	public function setItemsPerPage($num) {
		$this->itemsPerPage = $num;
	}
	
	public function setItems($items) {
		$this->items = $items;
	}

	public function setNameSpace($ns) {
		$this->queryStringPagingVariable .= '_' . $ns;
		$this->queryStringSortVariable .= '_' . $ns;
		$this->queryStringSortDirectionVariable .= '_' . $ns;		
	}	
	
	/** 
	 * Returns the total number of items found by this list
	 */
	public function getTotal() {
		if ($this->total == -1) {
			$this->total = count($this->items);
		}
		return $this->total;
	}
	
	/** 
	 * Returns an array of object by "page"
	 */
	public function getPage($page = false) {
		$this->setCurrentPage($page);
		$offset = 0;
		if ($this->currentPage > 1) {
			$offset = $this->itemsPerPage * ($this->currentPage - 1);
		}
		return $this->get($this->itemsPerPage, $offset);
	}

	public function get($itemsToGet = 0, $offset = 0) {
		$this->start = $offset;
		return array_slice($this->items, $offset, $itemsToGet);
	}
	
	private function setCurrentPage($page = false) {
		$this->currentPage = $page;
		if ($page == false) {
			$pagination = Loader::helper('pagination');
			$pagination->queryStringPagingVariable = $this->queryStringPagingVariable;
			$this->currentPage = $pagination->getRequestedPage();
		}
	}

	/** 
	 * Displays summary text about a list
	 */
	public function displaySummary( $right_content = '' ) {
		if ($this->getTotal() < 1) {
			return false;
		}
		$summary = $this->getSummary();
		$html = '<div class="ccm-paging-top">'. t('Viewing <b>%s</b> to <b>%s</b> (<b>%s</b> Total)', $summary->currentStart, "<span id=\"pagingPageResults\">" . $summary->currentEnd . "</span>", "<span id=\"pagingTotalResults\">" . $this->total . "</span>") . ( $right_content != '' ? '<span class="ccm-paging-top-content">'. $right_content .'</span>' : '' ) .'</div>';
		print $html;
	}
	
	public function getSearchResultsClass($field) {
		$class = '';
		if ($this->isActiveSortColumn($field)) {	
			$class = 'ccm-results-list-active-sort-';
			if ($this->getActiveSortDirection() == 'desc') {
				$class .= 'desc';
			} else {
				$class .= 'asc';
			}
		}
		return $class;
	}	

	public function getSortByURL($column, $dir = 'asc', $baseURL = false) {
		$uh = Loader::helper('url');
		
		// we switch it up if this column is the currently active column and the direction is currently the case
		if ($_REQUEST[$this->queryStringSortVariable] == $column && $_REQUEST[$this->queryStringSortDirectionVariable] == $dir) {
			$dir = ($dir == 'asc') ? 'desc' : 'asc';
		}
		$url = $uh->setVariable(array(
			$this->queryStringSortVariable => $column,
			$this->queryStringSortDirectionVariable => $dir
		), false, $baseURL);
		print $url;
	}
	
	public function isActiveSortColumn($column) {
		return ($this->getActiveSortColumn() == $column);
	}
	
	public function getActiveSortColumn() {
		return $this->sortBy;
	}
	
	public function getActiveSortDirection() {
		return $this->sortByDirection;
	}
	
	public function getPagination($url = false) {
		$pagination = Loader::helper('pagination');
		if ($this->currentPage == false) {
			$this->setCurrentPage();
		}
		$pagination->queryStringPagingVariable = $this->queryStringPagingVariable;
		$pagination->init($this->currentPage, $this->getTotal(), $url, $this->itemsPerPage);
		return $pagination;
	}
	
	/** 
	 * Gets standard HTML to display paging */
	public function displayPaging($script = false) {
		$summary = $this->getSummary();
		$paginator = $this->getPagination($script);
		if ($summary->pages > 1) {
			print '<div class="ccm-spacer"></div>';
			print '<div class="ccm-pagination">';
			print '<span class="ccm-page-left">' . $paginator->getPrevious() . '</span>';
			print '<span class="ccm-page-right">' . $paginator->getNext() . '</span>';
			print $paginator->getPages();
			print '</div>';	
		}
	}
	/** 
	 * Returns an object with properties useful for paging
	 */
	public function getSummary() {

		$ss = new stdClass;
		$ss->chunk = $this->itemsPerPage;
		$ss->order = $this->sortByDirection;
		
		$ss->startAt = $this->start;
		$ss->total = $this->getTotal();
		
		$ss->startAt = ($ss->startAt < $ss->chunk) ? '0' : $ss->startAt;
		$itc = intval($ss->total / $ss->chunk);
		$ss->pages = $itc + 1;
		
		if ($ss->startAt > 0) {
			$ss->current = ($ss->startAt / $ss->chunk ) + 1;
		} else {
			$ss->current = '1';
		}
		
		$ss->previous = ($ss->startAt >= $ss->chunk) ? ($ss->current - 2) * $ss->chunk : -1;
		$ss->next = (($ss->total - $ss->startAt) >= $ss->chunk) ? $ss->current * $ss->chunk : '';
		$ss->last = (($ss->total - $ss->startAt) >= $ss->chunk) ? ($ss->pages - 1) * $ss->chunk : '';
		$ss->currentStart = ($ss->current > 1) ? ((($ss->current - 1) * $ss->chunk) + 1) : '1';
		$ss->currentEnd = ((($ss->current + $ss->chunk) - 1) <= $ss->last) ? ($ss->currentStart + $ss->chunk) - 1 : $ss->total;			
		$ss->needsPaging = ($ss->total > $ss->chunk) ? true : false;
		return $ss;
	}
	/** 
	 * Sets up a column to sort by
	 */
	public function sortBy($column, $direction = 'asc') {
		$this->sortBy = $column;
		$this->sortByDirection = $direction;
	}

	/** 
	 * Sets up a column to sort by
	 */
	public function sortByMultiple() {
		$args = func_get_args();
		for ($i = 0; $i < count($args); $i++) {
			$this->sortByString .= $args[$i];
			if (($i + 1) < count($args)) { 
				$this->sortByString .= ', ';
			}
		}
	}

}