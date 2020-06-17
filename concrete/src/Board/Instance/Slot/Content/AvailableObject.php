<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Entity\Board\InstanceItem;

class AvailableObject
{

    /**
     * @var int
     */
    protected $slot = 0;

    /**
     * @var InstanceItem
     */
    protected $item;

    /**
     * @var ObjectInterface
     */
    protected $contentObject;

    /**
     * AvailableObject constructor.
     * @param int $slot
     * @param InstanceItem $item
     * @param ObjectInterface $contentObject
     */
    public function __construct(int $slot, InstanceItem $item, ObjectInterface $contentObject)
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
     * @return InstanceItem
     */
    public function getItem(): InstanceItem
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
