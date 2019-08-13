<?php
namespace Concrete\Core\Express\Entry\Search\Result;

use Concrete\Core\Search\Column\Column as BaseColumn;
use Concrete\Core\Search\Result\Result as SearchResult;

/**
 * @since 8.0.0
 */
class Result extends SearchResult
{
    public function getItemDetails($item)
    {
        $node = new Item($this, $this->listColumns, $item);

        return $node;
    }
}
