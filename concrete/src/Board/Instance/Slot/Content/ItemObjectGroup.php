<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Entity\Board\Item;

class ItemObjectGroup
{

    /**
     * @var Item 
     */
    protected $item;

    /**
     * @var ObjectInterface[] 
     */
    protected $contentObjects;
    
    public function __construct(Item $item, array $contentObjects)
    {
        $this->item = $item;
        $this->contentObjects = $contentObjects;
    }

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @return ObjectInterface[]
     */
    public function getContentObjects(): array
    {
        return $this->contentObjects;
    }
    
    
}
