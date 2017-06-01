<?php
namespace Concrete\Core\Search\ItemList;

use Concrete\Core\Search\StickyRequest;
use Pagerfanta\Exception\LessThan1CurrentPageException;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;

/**
 * Base class for all the list-related classes.
 */
abstract class ItemList
{
    /**
     * The name of the query string parameter to be used to identify the currently sorted column.
     *
     * @var string
     */
    protected $sortColumnParameter = 'ccm_order_by';

    /**
     * The name of the query string parameter to be used to identify the direction of the currently sorted column.
     *
     * @var string
     */
    protected $sortDirectionParameter = 'ccm_order_by_direction';

    /**
     * The name of the query string parameter to be used to identify the current page during paginated results.
     *
     * @var string
     */
    protected $paginationPageParameter = 'ccm_paging_p';

    /**
     * The currently sorted column.
     *
     * @var string|null
     */
    protected $sortBy;

    /**
     * The direction of the currently sorted column ('asc' or 'desc').
     *
     * @var string|null
     */
    protected $sortByDirection;

    /**
     * Can the sorting be set via query string parameters?
     *
     * @var bool
     */
    protected $enableAutomaticSorting = true;

    /**
     * The list of column names that can be sorted automatically by inspecting the query string parameters.
     *
     * @var string[]
     */
    protected $autoSortColumns = [];

    /**
     * The maximum number of items per page (-1 to use the default value set by the pagination object).
     *
     * @var int
     */
    protected $itemsPerPage = -1;

    /**
     * Is the debugging enabled?
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * Apply the currently sorted column.
     *
     * @param string $field the column name
     * @param string $direction The sorting direction ('asc' or 'desc')
     */
    abstract protected function executeSortBy($field, $direction = 'asc');

    /**
     * Set the currently sorted column (by verifying that the column name is valid).
     *
     * @param string $field the column name
     * @param string $direction The sorting direction ('asc' or 'desc')
     *
     * @throws \Exception Throws an exception if the column name is not acceptable
     */
    protected function executeSanitizedSortBy($field, $direction)
    {
        $this->executeSortBy($field, $direction);
    }

    /**
     * Get the list of the resulting raw results (for example the list of item identifiers).
     *
     * @return mixed[]
     */
    abstract public function executeGetResults();

    /**
     * Build the final result item.
     *
     * @param mixed $mixed one of the values returned by the executeGetResults() method
     *
     * @return mixed|null Returns null if the item couldn't be found/loaded, or the resulting item otherwise
     */
    abstract public function getResult($mixed);

    /**
     * Method called to start debugging (if the debug is enabled).
     */
    abstract public function debugStart();

    /**
     * Method called to start debugging (if the debug is enabled).
     */
    abstract public function debugStop();

    /**
     * Build the pagination object.
     *
     * @return \Concrete\Core\Search\Pagination\Pagination|\Concrete\Core\Search\Pagination\PermissionablePagination
     */
    abstract protected function createPaginationObject();

    /**
     * Enable debugging.
     */
    public function debug()
    {
        $this->debug = true;
    }

    /**
     * Is debugging enabled?
     *
     * @return bool
     */
    public function isDebugged()
    {
        return $this->debug;
    }

    /**
     * Set and apply the currently sorted column.
     *
     * @param string $field the column name
     * @param string $direction The sorting direction ('asc' or 'desc')
     */
    public function sortBy($field, $direction = 'asc')
    {
        $this->sortBy = $field;
        $this->sortByDirection = $direction;
        $this->executeSortBy($field, $direction);
    }

    /**
     * Set and apply the currently sorted column (by verifying that the column name is valid).
     *
     * @param string $field the column name
     * @param string $direction The sorting direction ('asc' or 'desc')
     */
    public function sanitizedSortBy($field, $direction = 'asc')
    {
        $this->sortBy = $field;
        $this->sortByDirection = $direction;
        $this->executeSanitizedSortBy($field, $direction);
    }

    /**
     * Returns a full array of result objects.
     *
     * @return mixed[]|null[]
     */
    public function getResults()
    {
        $results = [];

        $this->debugStart();

        $executeResults = $this->executeGetResults();

        $this->debugStop();

        foreach ($executeResults as $result) {
            $r = $this->getResult($result);
            if ($r != null) {
                $results[] = $r;
            }
        }

        return $results;
    }

    /**
     * Get the currently sorted column.
     *
     * @return string
     */
    public function getActiveSortColumn()
    {
        return $this->sortBy;
    }

    /**
     * Check if a column is the one we are currently sorting by.
     *
     * @param string $field
     *
     * @return bool
     */
    public function isActiveSortColumn($field)
    {
        return $this->sortBy == $field;
    }

    /**
     * Disable the sorting set via query string parameters.
     */
    public function disableAutomaticSorting()
    {
        $this->enableAutomaticSorting = false;
    }

    /**
     * Get the CSS class for a column, if it's the the one we are currently sorting by.
     *
     * @param string $column
     *
     * @return string|false return false if the column is not the current one
     */
    public function getSortClassName($column)
    {
        $class = false;
        if ($this->isActiveSortColumn($column)) {
            $class = 'ccm-results-list-active-sort-';
            if ($this->getActiveSortDirection() == 'desc') {
                $class .= 'desc';
            } else {
                $class .= 'asc';
            }
        }

        return $class;
    }

    /**
     * Create a full URL with the query string parameters controlling the sorting.
     *
     * @param string $column The column name
     * @param string $dir The default sorting direction (if $column is the current one, we'll invert the current sort criteria)
     * @param string $url The URL to which the query string parameter should be added (if falsy, we'll use the current request URL)
     *
     * @return string
     */
    public function getSortURL($column, $dir = 'asc', $url = false)
    {
        $uh = \Core::make('helper/url');
        /* @var \Concrete\Core\Utility\Service\Url $uh */
        if ($this->isActiveSortColumn($column) && $this->getActiveSortDirection() == $dir) {
            $dir = ($dir == 'asc') ? 'desc' : 'asc';
        }

        $args = [
            $this->getQuerySortColumnParameter() => $column,
            $this->getQuerySortDirectionParameter() => $dir,
        ];

        $url = $uh->setVariable($args, false, $url);

        return strip_tags($url);
    }

    /** @var \Concrete\Core\Search\Pagination\Pagination */
    protected $pagination;

    /**
     * Get the direction of the currently sorted column ('asc' or 'desc' or null).
     *
     * @return string|null
     */
    public function getActiveSortDirection()
    {
        return $this->sortByDirection;
    }

    /**
     * Get the name of the query string parameter to be used to identify the currently sorted column.
     *
     * @return string
     */
    public function getQuerySortColumnParameter()
    {
        return $this->sortColumnParameter;
    }

    /**
     * Get the name of the query string parameter to be used to identify the current page during paginated results.
     *
     * @return string
     */
    public function getQueryPaginationPageParameter()
    {
        return $this->paginationPageParameter;
    }

    /**
     * Get the name of the query string parameter to be used to identify the direction of the currently sorted column.
     *
     * @return string
     */
    public function getQuerySortDirectionParameter()
    {
        return $this->sortDirectionParameter;
    }

    /**
     * Get the maximum number of items per page (-1 to use the default value set by the pagination object).
     *
     * @param int $itemsPerPage
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * Returns the number of total results in this item list.
     *
     * @return int
     */
    abstract public function getTotalResults();

    /**
     * Create the pagination object and initialize its page size and index.
     *
     * @return \Concrete\Core\Search\Pagination\Pagination|\Concrete\Core\Search\Pagination\PermissionablePagination
     */
    public function getPagination()
    {
        $pagination = $this->createPaginationObject();
        if ($this->itemsPerPage > -1) {
            $pagination->setMaxPerPage($this->itemsPerPage);
        }
        $query = \Request::getInstance()->query;
        if ($query->has($this->getQueryPaginationPageParameter())) {
            $page = intval($query->get($this->getQueryPaginationPageParameter()));
            try {
                $pagination->setCurrentPage($page);
            } catch (LessThan1CurrentPageException $e) {
                $pagination->setCurrentPage(1);
            } catch (OutOfRangeCurrentPageException $e) {
                $pagination->setCurrentPage(1);
            }
        }

        return $pagination;
    }

    /**
     * Initializes the sorting column and direction by inspecting a StickyRequest or the current query string parameters.
     * If automatic sorting is disabled, this method does not do anything.
     *
     * @param StickyRequest $request the StickyRequest to use (if null: we'll use the query string parameters)
     */
    public function setupAutomaticSorting(StickyRequest $request = null)
    {
        if ($this->enableAutomaticSorting) {
            if ($request) {
                $data = $request->getSearchRequest();
            } else {
                $data = \Request::getInstance()->query->all();
            }
            $direction = 'asc';
            if (isset($data[$this->getQuerySortDirectionParameter()])) {
                $direction = $data[$this->getQuerySortDirectionParameter()];
            }
            if (isset($data[$this->getQuerySortColumnParameter()])) {
                $value = $data[$this->getQuerySortColumnParameter()];
                if (in_array($value, $this->autoSortColumns)) {
                    $this->sanitizedSortBy($value, $direction);
                }
            }
        }
    }

    /**
     * @deprecated Use the getResults method
     * @see ItemList::getResults()
     */
    public function get()
    {
        return $this->getResults();
    }
}
