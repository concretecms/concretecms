<?php
namespace Concrete\Core\Navigation\Item;

interface ItemInterface
{

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return bool
     */
    public function isActive(): bool;

    /**
     * @return bool
     */
    public function isActiveParent(): bool;

    /**
     * @return string
     */
    public function getUrl(): string;

    /**
     * @return \Concrete\Core\Navigation\Item\ItemInterface[]
     */
    public function getChildren(): array;

}
