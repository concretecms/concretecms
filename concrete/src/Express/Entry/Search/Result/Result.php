<?php
namespace Concrete\Core\Express\Entry\Search\Result;

use Concrete\Core\Search\Column\Column as BaseColumn;
use Concrete\Core\Search\Result\Result as SearchResult;

class Result extends SearchResult
{
    public function getItemDetails($item)
    {
        $node = new Item($this, $this->listColumns, $item);

        return $node;
    }

}
