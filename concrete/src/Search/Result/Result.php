<?php
namespace Concrete\Core\Search\Result;

use Concrete\Core\Search\Column\Set;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Pagerfanta\View\TwitterBootstrap3View;
use stdClass;

class Result
{
    protected $listColumns;
    protected $list;
    protected $baseURL;
    protected $breadcrumb;
    protected $query; // This is a search request, it gets rid of folders, etc...
    protected $filters; // These are search filters - they display folders, don't display a "reset search" label, etc...

    /** @var \Concrete\Core\Search\Pagination\Pagination */
    protected $pagination;

    protected $items;
    protected $fields;
    protected $columns;

    /**
     * @return mixed
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param mixed $filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }


    public function getItemListObject()
    {
        return $this->list;
    }

    /**
     * @return mixed
     */
    public function getBreadcrumb()
    {
        return $this->breadcrumb;
    }

    /**
     * @param mixed $breadcrumb
     */
    public function setBreadcrumb($breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }

    public function setBaseURL($url)
    {
        $this->baseURL = $url;
    }

    public function getBaseURL()
    {
        return $this->baseURL;
    }

    public function getSearchResultBulkMenus()
    {
        return false;
    }

    /**
     * @return Set
     */
    public function getListColumns()
    {
        return $this->listColumns;
    }

    public function __construct(Set $columns, ItemList $il, $url = null, $fields = array())
    {
        $this->listColumns = $columns;
        $this->list = $il;
        $this->baseURL = $url;
        $this->fields = $fields;
        $factory = new PaginationFactory(\Request::createFromGlobals());
        $this->pagination = $factory->createPaginationObject($il, PaginationFactory::PERMISSIONED_PAGINATION_STYLE_PAGER);
    }

    public function getItems()
    {
        if (!isset($this->items)) {
            $this->items = array();
            $items = $this->pagination->getCurrentPageResults();
            foreach ($items as $item) {
                $node = $this->getItemDetails($item);
                $this->items[] = $node;
            }
        }

        return $this->items;
    }

    public function getColumns()
    {
        if (!isset($this->columns)) {
            $this->columns = array();
            foreach ($this->listColumns->getColumns() as $column) {
                $node = $this->getColumnDetails($column);
                $this->columns[] = $node;
            }
        }

        return $this->columns;
    }

    public function getColumnDetails($column)
    {
        $node = new Column($this, $column);

        return $node;
    }

    public function getItemDetails($item)
    {
        $node = new Item($this, $this->listColumns, $item);

        return $node;
    }

    public function getSortURL($column, $dir = 'asc')
    {
        return $this->getItemListObject()->getSortURL($column, $dir, $this->getBaseURL());
    }

    public function getJSONObject()
    {
        $obj = new stdClass();
        $obj->items = array();
        foreach ($this->getItems() as $item) {
            $obj->items[] = $item;
        }
        foreach ($this->getColumns() as $column) {
            $obj->columns[] = $column;
        }
        $html = '';
        if ($this->pagination->haveToPaginate()) {
            $this->pagination->setBaseURL($this->getBaseURL());
            $html = $this->pagination->renderView('dashboard', [
                    'prev_message' => tc('Pagination', '&larr; Previous'),
                    'next_message' => tc('Pagination', 'Next &rarr;'),
                    'active_suffix' => '<span class="sr-only">' . tc('Pagination', '(current)') . '</span>']);
        }
        $obj->paginationTemplate = $html;
        $obj->fields = $this->fields;
        $obj->filters = $this->filters;
        $obj->query = $this->query;
        $obj->bulkMenus = $this->getSearchResultBulkMenus();
        $obj->baseUrl = (string) $this->getBaseURL();
        $obj->breadcrumb = $this->getBreadcrumb();

        return $obj;
    }
}
