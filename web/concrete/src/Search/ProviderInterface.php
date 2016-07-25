<?php
namespace Concrete\Core\Search;

use Concrete\Core\Application\EditResponse;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\Result\Result as SearchResult;

interface ProviderInterface
{
    function getCurrentColumnSet();
    function getAvailableColumnSet();
    function getCustomAttributeKeys();
    function getSessionNamespace();
}
