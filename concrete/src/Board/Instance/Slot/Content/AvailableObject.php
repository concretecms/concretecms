<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Board\Item\ItemProviderInterface;

class AvailableObject
{

    /**
     * @var int
     */
    protected $slot = 0;

    /**
     * @var ItemProviderInterface
     */
    protected $item;

    /**
     * @var ObjectInterface
     */
    protected $contentObject;

    /**
     * AvailableObject constructor.
     * @param int $slot
     * @param ItemProviderInterface $item
     * @param ObjectInterface $contentObject
     */
    public function __construct(int $slot, ItemProviderInterface $item, ObjectInterface $contentObject)
    {
        $this->slot = $slot;
        $this->item = $item;
        $this->contentObject = $contentObject;
    }

    /**
     * @return int
     */
    public function getSlot(): int
    {
        return $this->slot;
    }

    /**
     * @return ItemProviderInterface
     */
    public function getItem(): ItemProviderInterface
    {
        return $this->item;
    }

    /**
     * @return ObjectInterface
     */
    public function getContentObject(): ObjectInterface
    {
        return $this->contentObject;
    }


}
