<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Board\Item\ItemProviderInterface;
use Concrete\Core\Entity\Board\InstanceItem;

class ItemObjectGroup
{

    /**
     * @var ItemProviderInterface
     */
    protected $item;

    /**
     * @var ObjectInterface[]
     */
    protected $contentObjects;

    public function __construct(ItemProviderInterface $item, array $contentObjects)
    {
        $this->item = $item;
        $this->contentObjects = $contentObjects;
    }

    /**
     * @return Item
     */
    public function getItem(): ItemProviderInterface
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
