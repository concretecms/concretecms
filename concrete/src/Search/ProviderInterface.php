<?php
namespace Concrete\Core\Search;

use Concrete\Core\Application\EditResponse;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\Result\Result as SearchResult;

interface ProviderInterface
{
    function getBaseColumnSet();
    function getDefaultColumnSet();
    function getCurrentColumnSet();
    function getAvailableColumnSet();
    function getAllColumnSet();
    function createSearchResultObject($columns, $list);

    function getCustomAttributeKeys();
    function getSearchResultFromQuery(Query $query);
    function getItemList();

    function getSavedSearch();
}
