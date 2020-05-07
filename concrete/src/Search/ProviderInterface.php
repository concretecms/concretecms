<?php
namespace Concrete\Core\Search;

use Concrete\Core\Application\EditResponse;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Search\Field\ManagerInterface;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Search\Result\Result as SearchResult;
use Concrete\Core\Search\Result\Result;

interface ProviderInterface
{
    /**
     * @return Set
     */
    function getBaseColumnSet();

    function getDefaultColumnSet();
    function getCurrentColumnSet();
    function getAvailableColumnSet();
    function getAllColumnSet();

    /**
     * @param $columns
     * @param $list
     * @return Result
     */
    function createSearchResultObject($columns, $list);

    function getCustomAttributeKeys();
    function getSearchResultFromQuery(Query $query);

    function getItemsPerPage();

    /**
     * @return array
     */
    function getItemsPerPageOptions();

    /**
     * @return ItemList
     */
    function getItemList();

    function getSavedSearch();

    /**
     * @return ManagerInterface
     */
    function getFieldManager();
}
