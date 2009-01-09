<?
/**
*
* A base class for working with lists of objects. Implements things like pagination & offset, getting the total, and iteration
* @package Utilities
*/
defined('C5_EXECUTE') or die(_("Access Denied."));

class ItemList {

	private $total = -1; // initial state == unknown
	protected $itemsPerPage = 20;
	private $currentPage = false;
	private $start = 0;
	private $sortBy;
	private $sortByDirection;
	private $query = '';
	private $debug = false;
	private $filters = array();
	private $queryStringPagingVariable = 'ccm_paging_p';
	
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
	public function getPage($page = false) {
		$this->setCurrentPage($page);
		$offset = 0;
		if ($this->currentPage > 1) {
			$offset = $this->itemsPerPage * ($this->currentPage - 1);
		}
		return $this->get($this->itemsPerPage, $offset);
	}
	
	private function setCurrentPage($page = false) {
		$this->currentPage = $page;
		if ($page == false) {
			$pagination = Loader::helper('pagination');
			$this->currentPage = $pagination->getRequestedPage();
		}
	}

	public function debug($dbg = true) {
		$this->debug = $dbg;
	}
	
	/** 
	 * Displays summary text about a list
	 */
	public function displaySummary() {
		if ($this->getTotal() < 1) {
			return false;
		}
		
		$summary = $this->getSummary();
		$html = '<div class="ccm-paging-top">' . t('Viewing <b>%s</b> to <b>%s</b> (<b>%s</b> Total)', $summary->currentStart, "<span id=\"pagingPageResults\">" . $summary->currentEnd . "</span>", "<span id=\"pagingTotalResults\">" . $this->total . "</span>") . '</div>';
		print $html;
	}
	
	public function getPagination($url) {
		$pagination = Loader::helper('pagination');
		if ($this->currentPage == false) {
			$this->setCurrentPage();
		}
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

	private function executeBase() {
		$v = array();		
		$q = $this->query . ' where 1=1 ';
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
		
		return array($q, $v);
	}
	
	/** 
	 * Returns an array of whatever objects extends this class (e.g. PageList returns a list of pages).
	 */
	public function get($itemsToGet = 0, $offset = 0) {
		$arr = $this->executeBase(); // returns an associated array of query/placeholder values
		$q = $arr[0];
		$v = $arr[1];
		// handle order by 
		if ($this->sortBy != '') {
			$q .= 'order by ' . $this->sortBy . ' ' . $this->sortByDirection . ' ';
		}
		if ($this->itemsPerPage > 0) {
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
		
		$this->start = $offset;
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