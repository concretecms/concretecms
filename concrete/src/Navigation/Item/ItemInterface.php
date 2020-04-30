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
     * @return string
     */
    public function getURL(): string;

    /**
     * @return Item[]
     */
    public function getChildren(): array;

}
