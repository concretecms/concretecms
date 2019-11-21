<?php
namespace Concrete\Core\Board\ItemCollection;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\Item;
use Doctrine\Common\Collections\ArrayCollection;

class ItemCollection
{

    /**
     * @var ArrayCollection
     */
    protected $items;

    /**
     * @var Board
     */
    protected $board;
    
    public function __construct(Board $board)
    {
        $this->board = $board;
        $this->items = new ArrayCollection();
    }

    public function setItem(int $slot, Item $item)
    {
        $this->items->set($slot, $item);
    }

    public function getItems()
    {
        return $this->items->toArray();
    }

    public function get($key)
    {
        return $this->items->get($key);
    }
    
    /**
     * @return Board
     */
    public function getBoard(): Board
    {
        return $this->board;
    }
    
    

}
