<?php
namespace Concrete\Core\Search\ItemList\Pager\Manager;

use Concrete\Core\Http\Request;
use Concrete\Core\Search\ItemList\Column;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\OffsetStartVariable;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableInterface;
use Concrete\Core\Search\Pagination\Adapter\PagerAdapter;
use Concrete\Core\Search\Pagination\PagerPagination;
use Concrete\Core\Search\StickyRequest;

abstract class AbstractPagerManager implements PagerManagerInterface
{

    protected $itemList;

    abstract public function getNextValue(Column $column, $mixed);

    /**
     * AbstractPagerManager constructor.
     * @param $itemList
     */
    public function __construct(ItemList $itemList)
    {
        $this->itemList = $itemList;
    }

    public function getNextPageVariables(PagerProviderInterface $itemList, PagerPagination $pagination)
    {
        /**
         * @var $adapter PagerAdapter
         */
        $adapter = $pagination->getAdapter();
        $return = array();
        $orderBy = $itemList->getOrderByColumns();
        foreach($orderBy as $column) {
            $variable = new OffsetStartVariable($column->getKey(), $this->getNextValue($column, $adapter->getLastResult()));
            $return[] = $variable;
        }

        return $return;
    }

    public function filterByVariable(VariableInterface $variable, PagerProviderInterface $itemList)
    {
        $sort = $itemList->getQuerySortDirectionParameter() == 'asc' ? '>' : '<';
        $where = sprintf('%s %s :offset', $variable->getName(), $sort);
        $query = $itemList->getQueryObject();
        $query->andWhere($where);
        $query->setParameter('offset', $variable->getValue());
    }


}