<?php
namespace Concrete\Core\Board\ItemCollection;

use Concrete\Core\Entity\Board\Board;

class Populator
{
    
    public function getItemCollection(Board $board)
    {
        $collection = new ItemCollection($board);
        $slot = 1;
        foreach($board->getItems() as $item) {
            $collection->setItem($slot, $item);
            $slot++;
        }
        return $collection;
    }
    
}
