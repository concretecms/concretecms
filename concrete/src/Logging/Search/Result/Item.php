<?php
namespace Concrete\Core\Logging\Search\Result;

use Concrete\Core\Logging\LogEntry;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Search\Result\Item as SearchResultItem;
use Concrete\Core\Search\Result\Result as SearchResult;

class Item extends SearchResultItem
{
    public $logId;

    public function __construct(SearchResult $result, Set $columns, $item)
    {
        parent::__construct($result, $columns, $item);
        $this->populateDetails($item);
    }

    /**
     * @param LogEntry $item
     */
    protected function populateDetails($item)
    {
        $this->logId = $item->getId();
    }
}
