<?php
namespace Concrete\Core\Legacy;
use Session;

class ItemList {

	protected $total = -1; // initial state == unknown
	protected $itemsPerPage = 20;
	protected $currentPage = false;
	protected $start = 0;
	protected $sortBy;
	protected $sortByDirection;
	protected $queryStringPagingVariable;
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

	public function getQueryStringSortVariable() {
		return $this->queryStringSortVariable;
	}

	public function getQueryStringSortDirectionVariable() {
		return $this->queryStringSortDirectionVariable;
	}

	protected function getStickySearchNameSpace() {
		return get_class($this) . $namespace . 'SearchFields';
	}

	public function resetSearchRequest($namespace = '') {
		Session::set($this->getStickySearchNameSpace(), array());
	}

	public function addToSearchRequest($key, $value) {
		$data = Session::get($this->getStickySearchNameSpace());
		if (!is_array($data)) {
			$data = array();
		}
		$data[$key] = $value;
		Session::set($this->getStickySearchNameSpace(), $data);
	}

	public function getSearchRequest() {
		if ($this->enableStickySearchRequest) {
			$data = Session::get($this->getStickySearchNameSpace());
			if (!is_array($data)) {
				$data = array();
			}

			// i don't believe we need this approach particularly, and it's a pain in the ass
			//$validSearchKeys = array('fKeywords', 'numResults', 'fsIDNone', 'fsID', 'ccm_order_dir', 'ccm_order_by', 'size_from', 'size_to', 'type', 'extension', 'date_from', 'date_to', 'searchField', 'selectedSearchField', 'akID');

			foreach($_REQUEST as $key => $value) {
				$data[$key] = $value;
			}
			Session::set($this->getStickySearchNameSpace(), $data);
			return $data;
		} else {
			return $_REQUEST;
		}
	}

	public function setItemsPerPage($num) {
		if (Loader::helper('validation/numbers')->integer($num)) {
			$this->itemsPerPage = $num;
		}
	}

	public function getItemsPerPage() {
		return $this->itemsPerPage;
	}

	public function setItems($items) {
		$this->items = $items;
	}

    protected function loadQueryStringPagingVariable()
    {
        $this->queryStringPagingVariable = \Config::get('concrete.seo.paging_string');
    }

	public function setNameSpace($ns) {
        if (!isset($this->queryStringPagingVariable)) {
            $this->loadQueryStringPagingVariable();
        }
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
			$offset = min($this->itemsPerPage * ($this->currentPage - 1), 2147483647);
		}
		return $this->get($this->itemsPerPage, $offset);
	}

	public function get($itemsToGet = 0, $offset = 0) {
		$this->start = $offset;
		if ($itemsToGet == -1) {
			return $this->items;
		}

		return array_slice($this->items, $offset, $itemsToGet);
	}

	protected function setCurrentPage($page = false) {
		$this->currentPage = $page;
		if ($page == false) {
			$pagination = Loader::helper('pagination');
            if (!isset($this->queryStringPagingVariable)) {
                $this->loadQueryStringPagingVariable();
            }
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
			$args[$k] = Loader::helper('text')->alphanum($v);
		}
		$url = $uh->setVariable($args, false, $baseURL);
		return strip_tags($url);
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
        if (!isset($this->queryStringPagingVariable)) {
            $this->loadQueryStringPagingVariable();
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
			$html .= '<div class="ccm-search-results-pagination"><ul class="pagination">';
			$prevClass = 'prev';
			$nextClass = 'next';
			if (!$paginator->hasPreviousPage()) {
				$prevClass = 'prev disabled';
			}
			if (!$paginator->hasNextPage()) {
				$nextClass = 'next disabled';
			}
			$html .= '<li class="' . $prevClass . '">' . $paginator->getPrevious(false, 'a') . '</li>';
			$html .= $paginator->getPages('li');
			$html .= '<li class="' . $nextClass . '">' . $paginator->getNext(false, 'a') . '</li>';
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

		$ss = new \stdClass;
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
	 * Note that this is overrides any previous sortByMultiple() call, and all sortBy() calls. Alternatively, you can pass a single
	 * array with multiple columns to sort by as its values.
	 * e.g. $list->sortByMultiple('columna desc', 'columnb asc');
	 * or $list->sortByMultiple(array('columna desc', 'columnb asc'));
	 */
	public function sortByMultiple() {
		$args = func_get_args();
		if(count($args) == 1 && is_array($args[0])) {
			$args = $args[0];
		}
		$this->sortByString = implode(', ', $args);
	}
}
