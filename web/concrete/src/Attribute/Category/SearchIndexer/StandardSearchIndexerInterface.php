<?php
namespace Concrete\Core\Attribute\Category\SearchIndexer;

interface StandardSearchIndexerInterface
{
    public function getIndexedSearchTable();
    public function getSearchIndexFieldDefinition();
    public function getIndexedSearchPrimaryKeyValue($mixed);
}
