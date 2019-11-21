<?php

namespace Concrete\Core\Board\Template;

use Concrete\Core\Board\ItemCollection\ItemCollection;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\Template;

class TemplateInstance
{

    /**
     * @var Board 
     */
    protected $board;

    /**
     * @var ItemCollection 
     */
    protected $collection;
    
    public function __construct(ItemCollection $collection, Template $template)
    {
        $this->collection = $collection;
        $this->template = $template;
    }

    /**
     * @return Board
     */
    public function getBoard(): Board
    {
        return $this->board;
    }

    /**
     * @return ItemCollection
     */
    public function getCollection(): ItemCollection
    {
        return $this->collection;
    }
    
    
    
}
