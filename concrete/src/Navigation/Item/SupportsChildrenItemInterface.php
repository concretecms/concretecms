<?php
namespace Concrete\Core\Navigation\Item;

use HtmlObject\Traits\Tag;

interface SupportsChildrenItemInterface
{

    /**
     * @return Item[]
     */
    public function getChildren(): array;

    public function setChildren(array $children);

    public function addChild(ItemInterface $item);

    public function setIsActive(bool $isActive): void;

    public function isActive(): bool;

    public function isActiveParent(): bool;

    public function setIsActiveParent(bool $isActiveParent): void;


}
