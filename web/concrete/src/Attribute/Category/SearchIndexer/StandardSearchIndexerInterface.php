<?php

namespace Concrete\Core\Attribute\Category\SearchIndexer;

use Concrete\Core\Database\Connection\Connection;
use Doctrine\DBAL\Schema\Schema;

interface StandardSearchIndexerInterface
{
    public function getIndexedSearchTable();
    public function getSearchIndexFieldDefinition();
    public function getIndexedSearchPrimaryKeyValue($mixed);
}