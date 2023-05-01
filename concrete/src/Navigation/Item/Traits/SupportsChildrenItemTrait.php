<?php
namespace Concrete\Core\Navigation\Item\Traits;

use Concrete\Core\Navigation\Item\ItemInterface;

trait SupportsChildrenItemTrait
{

    /**
     * @var bool
     */
    protected $isActive = false;

    /**
     * @var bool
     */
    protected $isActiveParent = false;


    /**
     * @var array
     */
    protected $children = [];

    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param array $children
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    public function addChild(ItemInterface $item)
    {
        $this->children[] = $item;
    }


    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return bool
     */
    public function isActiveParent(): bool
    {
        return $this->isActiveParent;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }


    /**
     * @param bool $isActiveParent
     */
    public function setIsActiveParent(bool $isActiveParent): void
    {
        $this->isActiveParent = $isActiveParent;
    }

    public function __clone()
    {
        $children = $this->getChildren();
        $newChildren = [];
        foreach($children as $child) {
            $newChildren[] = clone $child;
        }
        $this->setChildren($newChildren);
    }


}
