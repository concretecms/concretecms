<?
/**
*
* @package Utilities
*/
defined('C5_EXECUTE') or die("Access Denied.");

class DatabaseItemList extends ItemList {

	protected $query = '';
	protected $userQuery = '';
	protected $debug = false;
	protected $filters = array();
	protected $sortByString = '';
	protected $groupByString = '';  
	protected $havingString = '';  
	protected $autoSortColumns = array();
	protected $userPostQuery = '';
	
	public function getTotal() {
		if ($this->total == -1) {
			$db = Loader::db();
			$arr = $this->executeBase(); // returns an associated array of query/placeholder values				
			$r = $db->Execute($arr);
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
	
	public function addToQuery($query) {
		$this->userQuery .= $query . ' ';
	}

	protected function setupAutoSort() {
		if (count($this->autoSortColumns) > 0) {
			$req = $this->getSearchRequest();
			if (in_array($req[$this->queryStringSortVariable], $this->autoSortColumns)) {
				$this->sortBy($req[$this->queryStringSortVariable], $req[$this->queryStringSortDirectionVariable]);
			}
		}
	}
	
	protected function executeBase() {
		$db = Loader::db();
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
					if (count($value) > 0) {
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
							$q .= $db->quote($value[$i]);
						}
						$q .= ') ';
					} else {
						$q .= 'and 1 = 2';
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
	
	protected function setupSortByString() {
		if ($this->sortByString == '' && $this->sortBy != '') {
			$this->sortByString = $this->sortBy . ' ' . $this->sortByDirection;
		}
	}
	
	protected function setupAttributeSort() {
		if (is_callable(array($this->attributeClass, 'getList'))) {
			$l = call_user_func(array($this->attributeClass, 'getList'));
			foreach($l as $ak) {
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
	public function get($itemsToGet = 0, $offset = 0) {
		$q = $this->executeBase();
		// handle order by
		$this->setupAttributeSort();
		$this->setupAutoSort();
		$this->setupSortByString();
		
		if ($this->sortByString != '') {
			$q .= 'order by ' . $this->sortByString . ' ';
		}	
		if ($this->itemsPerPage > 0 && (intval($itemsToGet) || intval($offset)) ) {
			$q .= 'limit ' . $offset . ',' . $itemsToGet . ' ';
		}
		
		$db = Loader::db();
		if ($this->debug) { 
			Database::setDebug(true);
		}
		//echo $q.'<br>'; 
		$resp = $db->GetAll($q);
		if ($this->debug) { 
			Database::setDebug(false);
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
	
	public function getSearchResultsClass($field) {
		if ($field instanceof AttributeKey) {
			$field = 'ak_' . $field->getAttributeKeyHandle();
		}
		return parent::getSearchResultsClass($field);
	}

	public function sortBy($key, $dir) {
		if ($key instanceof AttributeKey) {
			$key = 'ak_' . $key->getAttributeKeyHandle();
		}
		parent::sortBy($key, $dir);
	}
	
	public function groupBy($key) {
		if ($key instanceof AttributeKey) {
			$key = 'ak_' . $key->getAttributeKeyHandle();
		}
		$this->groupByString = $key;
	}	

	public function having($column, $value, $comparison = '=') {
		if ($column == false) {
			$this->havingString = $value;
		} else {
			$this->havingString = $column . ' ' . $comparison . ' ' . $value;
		}
	}
	
	public function getSortByURL($column, $dir = 'asc', $baseURL = false, $additionalVars = array()) {
		if ($column instanceof AttributeKey) {
			$column = 'ak_' . $column->getAttributeKeyHandle();
		}
		return parent::getSortByURL($column, $dir, $baseURL, $additionalVars);
	}
	
	protected function setupAttributeFilters($join) {
		$db = Loader::db();
		$i = 1;
		$this->addToQuery($join);
		foreach($this->attributeFilters as $caf) {
			$this->filter($caf[0], $caf[1], $caf[2]);
		}
	}

	public function filterByAttribute($column, $value, $comparison = '=') {
		if (is_array($column)) {
			$column = $column[key($column)] . '_' . key($column);
		}
		$this->attributeFilters[] = array('ak_' . $column, $value, $comparison);
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
	protected $queryStringPagingVariable = PAGING_STRING;
	protected $queryStringSortVariable = 'ccm_order_by';
	protected $queryStringSortDirectionVariable = 'ccm_order_dir';
	protected $enableStickySearchRequest = false;
	protected $stickySearchRequestNameSpace = '';
	protected $items = array();
	
	public function enableStickySearchRequest($namespace = false) {
		if ($namespace) {
			$this->stickySearchRequestNameSpace = $namespace;
		}
		$this->enableStickySearchRequest = true;
	}
	
	public function getQueryStringPagingVariable() {
		return $this->queryStringPagingVariable;
	}

	public function getQueryStringSortVariable() {
		return $this->queryStringSortVariable;
	}

	public function getQueryStringSortDirectionVariable() {
		return $this->queryStringSortDirectionVariable;
	}
	
	public function resetSearchRequest($namespace = '') {
		$_SESSION[get_class($this) . $namespace . 'SearchFields'] = array();
	}
	
	public function addToSearchRequest($key, $val) {
		$_SESSION[get_class($this) . $this->stickySearchRequestNameSpace . 'SearchFields'][$key] = $value;
	}
	
	public function getSearchRequest() {
		if ($this->enableStickySearchRequest) {
			if (!is_array($_SESSION[get_class($this) . $this->stickySearchRequestNameSpace . 'SearchFields'])) {
				$_SESSION[get_class($this) . $this->stickySearchRequestNameSpace . 'SearchFields'] = array();
			}
			
			// i don't believe we need this approach particularly, and it's a pain in the ass
			//$validSearchKeys = array('fKeywords', 'numResults', 'fsIDNone', 'fsID', 'ccm_order_dir', 'ccm_order_by', 'size_from', 'size_to', 'type', 'extension', 'date_from', 'date_to', 'searchField', 'selectedSearchField', 'akID');
			
			foreach($_REQUEST as $key => $value) {
				$_SESSION[get_class($this) . $this->stickySearchRequestNameSpace . 'SearchFields'][$key] = $value;
			}		
			return $_SESSION[get_class($this) . $this->stickySearchRequestNameSpace . 'SearchFields'];
		} else {
			return $_REQUEST;
		}
	}
	
	public function setItemsPerPage($num) {
		$this->itemsPerPage = $num;
	}
	
	public function getItemsPerPage() {
		return $this->itemsPerPage;
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
	
	protected function setCurrentPage($page = false) {
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
		if ($summary->currentEnd == -1) {
			$html = '<div class="ccm-paging-top">'. t('Viewing <b>%s</b> to <b>%s</b> (<b>%s</b> Total)', $summary->currentStart, "<span id=\"pagingPageResults\">" . $summary->total . "</span>", "<span id=\"pagingTotalResults\">" . $this->total . "</span>") . ( $right_content != '' ? '<span class="ccm-paging-top-content">'. $right_content .'</span>' : '' ) .'</div>';
		} else {
			$html = '<div class="ccm-paging-top">'. t('Viewing <b>%s</b> to <b>%s</b> (<b>%s</b> Total)', $summary->currentStart, "<span id=\"pagingPageResults\">" . $summary->currentEnd . "</span>", "<span id=\"pagingTotalResults\">" . $this->total . "</span>") . ( $right_content != '' ? '<span class="ccm-paging-top-content">'. $right_content .'</span>' : '' ) .'</div>';
		}
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

	public function getSortByURL($column, $dir = 'asc', $baseURL = false, $additionalVars = array()) {
		$uh = Loader::helper('url');
		
		// we switch it up if this column is the currently active column and the direction is currently the case
		if ($this->sortBy == $column && $this->sortByDirection == $dir) {
			$dir = ($dir == 'asc') ? 'desc' : 'asc';
		}
		$args = array(
			$this->queryStringSortVariable => $column,
			$this->queryStringSortDirectionVariable => $dir
		);

		foreach($additionalVars as $k => $v) {
			$args[$k] = $v;
		}
		$url = $uh->setVariable($args, false, $baseURL);
		print strip_tags($url);
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
	
	public function requiresPaging() {
		$summary = $this->getSummary();
		return $summary->pages > 1;
	}
	
	public function getPagination($url = false, $additionalVars = array()) {
		$pagination = Loader::helper('pagination');
		if ($this->currentPage == false) {
			$this->setCurrentPage();
		}
		if (count($additionalVars) > 0) {
			$pagination->setAdditionalQueryStringVariables($additionalVars);
		}
		$pagination->queryStringPagingVariable = $this->queryStringPagingVariable;
		$pagination->init($this->currentPage, $this->getTotal(), $url, $this->itemsPerPage);
		return $pagination;
	}

	/** 
	 * Gets paging that works in our new format */
	public function displayPagingV2($script = false, $return = false, $additionalVars = array()) {
		$summary = $this->getSummary();
		$paginator = $this->getPagination($script, $additionalVars);
		if ($summary->pages > 1) {
			$html .= '<div class="pagination ccm-pagination"><ul>';
			$html .= '<li class="prev">' . $paginator->getPrevious() . '</li>';
			$html .= $paginator->getPages('li');
			$html .= '<li class="next">' . $paginator->getNext() . '</li>';
			$html .= '</ul></div>';
		}
		if (isset($html)) {
			if ($return) {
				return $html;
			} else {
				print $html;
			}
		}
	}
	
	/** 
	 * Gets standard HTML to display paging */
	public function displayPaging($script = false, $return = false, $additionalVars = array()) {
		$summary = $this->getSummary();
		$paginator = $this->getPagination($script, $additionalVars);
		if ($summary->pages > 1) {
			$html = '<div class="ccm-spacer"></div>';
			$html .= '<div class="ccm-pagination">';
			$html .= '<span class="ccm-page-left">' . $paginator->getPrevious() . '</span>';
			$html .= $paginator->getPages();
			$html .= '<span class="ccm-page-right">' . $paginator->getNext() . '</span>';
			$html .= '</div>';
		}
		if (isset($html)) {
			if ($return) {
				return $html;
			} else {
				print $html;
			}
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
		if ($ss->total == $ss->chunk) {
			$itc = 0;
		}
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
	 * Sets column to sort by. Only supports a single column; for multiple columns us sortByMultiple()
	 * @param string $column Column name to sort
	 * @param string $direction Sorting direction. Use 'asc' or 'desc'
	 */
	public function sortBy($column, $direction = 'asc') {
		$this->sortBy = $column;
		if (in_array(strtolower($direction), array('asc','desc'))) {
			$this->sortByDirection = $direction;
		} else {
			$this->sortByDirection = 'asc';
		}
	}
	
	public function getSortBy() {return $this->sortBy;}
	public function getSortByDirection() {return $this->sortByDirection;}

	/** 
	 * Sets up a multiple columns to search by. Each argument is taken "as-is" (including asc or desc) and concatenated with commas
	 * Note that this is overrides any previous sortByMultiple() call, and all sortBy() calls
	 */
	public function sortByMultiple() {
		$args = func_get_args();
		$this->sortByString = implode(', ', $args);
	}
}

class DatabaseItemListColumn {

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
	public function __construct($key, $name, $callback, $isSortable = true, $defaultSort = 'asc') {
		$this->columnKey = $key;
		$this->columnName = $name;
		$this->isSortable = $isSortable;
		$this->callback = $callback;
		$this->defaultSortDirection = $defaultSort;
	}
}

class DatabaseItemListAttributeKeyColumn extends DatabaseItemListColumn {

	protected $attributeKey = false;
	
	public function getAttributeKey() {
		return $this->attributeKey;
	}

	public function __construct($attributeKey, $isSortable = true, $defaultSort = 'asc') {
		$this->attributeKey = $attributeKey;
		parent::__construct('ak_' . $attributeKey->getAttributeKeyHandle(), $attributeKey->getAttributeKeyName(), false, $isSortable, $defaultSort);
	}
	
	public function getColumnValue($obj) {
		if (is_object($this->attributeKey)) {
			$vo = $obj->getAttributeValueObject($this->attributeKey);
			if (is_object($vo)) {
				return $vo->getValue('display');
			}
		}
	}
}

class DatabaseItemListColumnSet {
	
	protected $columns = array();
	protected $defaultSortColumn;
	
	public function addColumn($col) {
		$this->columns[] = $col;
	}
	public function getSortableColumns() {
		$tmp = array();
		$columns = $this->getColumns();
		foreach($columns as $col) {
			if ($col->isColumnSortable()) {
				$tmp[] = $col;
			}
		}
		return $tmp;
	}
	public function setDefaultSortColumn(DatabaseItemListColumn $col, $direction = false) {
		if ($direction != false) {
			$col->setColumnDefaultSortDirection($direction);
		}
		$this->defaultSortColumn = $col;
	}
	
	public function getDefaultSortColumn() {
		return $this->defaultSortColumn;
	}
	public function getColumnByKey($key) {
		if (substr($key, 0, 3) == 'ak_') {
			$ak = call_user_func(array($this->attributeClass, 'getByHandle'), substr($key, 3));
			$col = new DatabaseItemListAttributeKeyColumn($ak);
			return $col;
		} else {
			foreach($this->columns as $col) {
				if ($col->getColumnKey() == $key) {
					return $col;			
				}
			}
		}
	}
	public function getColumns() {return $this->columns;}
	public function contains($col) {
		foreach($this->columns as $_col) {
			if ($col instanceof DatabaseItemListColumn) {
				if ($_col->getColumnKey() == $col->getColumnKey()) {
					return true;
				}
			} else if (is_a($col, 'AttributeKey')) {
				if ($_col->getColumnKey() == 'ak_' . $col->getAttributeKeyHandle()) {
					return true;
				}
			}
		}
		return false;
	}
}
