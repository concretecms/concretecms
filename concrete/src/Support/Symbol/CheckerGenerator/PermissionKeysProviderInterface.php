<?php

declare(strict_types=1);

namespace Concrete\Core\Support\Symbol\CheckerGenerator;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Provides the list of the permission key categories and permission keys to the CheckerGenerator.
 */
interface PermissionKeysProviderInterface
{
    /**
     * Get the handles of all the permission key categories.
     *
     * @return string[]
     */
    public function getCategoryHandles(): array;

    /**
     * Get the permission keys of a permission key category.
     *
     * @return \Concrete\Core\Support\Symbol\CheckerGenerator\PermissionKey[]
     */
    public function getKeys(string $categoryHandle): array;
}
