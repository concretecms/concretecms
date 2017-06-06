<?php
namespace Concrete\Core\Search\ItemList\Pager\Manager;

use Concrete\Core\Http\Request;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\OffsetStartVariable;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableInterface;
use Concrete\Core\Search\Pagination\Adapter\PagerAdapter;
use Concrete\Core\Search\Pagination\PagerPagination;
use Concrete\Core\Search\StickyRequest;

abstract class AbstractPagerManager implements PagerManagerInterface
{

    protected $itemList;

    abstract public function getCursorStartValue($mixed);
    abstract public function getCursorObject($cursor);

    /**
     * AbstractPagerManager constructor.
     * @param $itemList
     */
    public function __construct(ItemList $itemList)
    {
        $this->itemList = $itemList;
    }

    public function getNextCursorStart(PagerProviderInterface $itemList, PagerPagination $pagination)
    {
        /**
         * @var $adapter PagerAdapter
         */
        $adapter = $pagination->getAdapter();
        $result = $adapter->getLastResult();
        if ($result) {
            return $this->getCursorStartValue($result);
        }
    }

    public function displaySegmentAtCursor($cursor, PagerProviderInterface $itemList)
    {
        if (!is_object($cursor)) {
            $object = $this->getCursorObject($cursor);
        } else {
            $object = $cursor;
        }
        if ($object) {
            // Figure out what we are sorting by
            $columns = $itemList->getOrderByColumns($this->getAvailableColumnSet());
            foreach($columns as $column) {
                if ($column instanceof PagerColumnInterface) {
                    $column->filterListAtOffset($itemList, $object);
                }
            }
        }
    }


}