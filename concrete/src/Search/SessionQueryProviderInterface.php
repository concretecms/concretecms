<?php
namespace Concrete\Core\Search;

use Concrete\Core\Application\EditResponse;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\Result\Result as SearchResult;

interface SessionQueryProviderInterface
{

    function setSessionCurrentQuery(Query $query);
    function getSessionCurrentQuery();
    function clearSessionCurrentQuery();
    function getSessionNamespace();


}
