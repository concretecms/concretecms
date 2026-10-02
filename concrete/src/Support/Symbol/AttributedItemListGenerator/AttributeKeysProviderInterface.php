<?php

declare(strict_types=1);

namespace Concrete\Core\Support\Symbol\AttributedItemListGenerator;

defined('C5_EXECUTE') or die('Access Denied.');

interface AttributeKeysProviderInterface
{
    /**
     * Get the handles of the attribute key categories.
     *
     * @return string[]
     */
    public function getCategoryHandles(): array;

    /**
     * Get the attribute keys of a category.
     *
     * @return \Concrete\Core\Support\Symbol\AttributedItemListGenerator\AttributeKey[]
     */
    public function getKeys(string $categoryHandle): array;
}
