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
    public function getUrl(): string;

    /**
     * @return Item[]
     */
    public function getChildren(): array;

}
