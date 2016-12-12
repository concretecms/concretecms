<?php
namespace Concrete\Core\Express\Entry\Search\Result;

use Concrete\Core\Search\Result\Item as SearchResultItem;
use Concrete\Core\Search\Result\ItemColumn as SearchResultItemColumn;
use Concrete\Core\Search\Result\Result as SearchResult;
use Concrete\Core\Search\Column\Set;

class Item extends SearchResultItem
{

    protected $entry;

    public function __construct(SearchResult $result, Set $columns, $item)
    {
        parent::__construct($result, $columns, $item);
        $this->populateDetails($item);
    }

    protected function populateDetails($entry)
    {
        $this->entry = $entry;
        $this->id = $entry->getID();
    }

    public function getEntry()
    {
        return $this->entry;
    }
}
