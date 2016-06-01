<?php
namespace Concrete\Core\Search;

use Concrete\Core\Application\EditResponse;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\Result\Result as SearchResult;
use Concrete\Core\Search\Result\Result;

interface QueryableInterface
{
    /**
     * @param Query $query
     * @return Result
     */
    function getSearchResultFromQuery(Query $query);

}
