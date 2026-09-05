<?php

declare(strict_types=1);

namespace Concrete\Core\Support\Symbol\CheckerGenerator;

use Concrete\Core\Permission\Category;
use Concrete\Core\Permission\Key\Key;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Read the permission key categories and permission keys from the database (for installed Concrete instances).
 */
final class DatabasePermissionKeysProvider implements PermissionKeysProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Support\Symbol\CheckerGenerator\PermissionKeysProviderInterface::getCategoryHandles()
     */
    public function getCategoryHandles(): array
    {
        $result = [];
        foreach (Category::getList() as $category) {
            $result[] = $category->getPermissionKeyCategoryHandle();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Support\Symbol\CheckerGenerator\PermissionKeysProviderInterface::getKeys()
     */
    public function getKeys(string $categoryHandle): array
    {
        $result = [];
        if (Category::getByHandle($categoryHandle)) {
            foreach (Key::getList($categoryHandle) as $key) {
                $result[] = new PermissionKey(
                    $categoryHandle,
                    (string) $key->getPermissionKeyHandle(),
                    (string) $key->getPermissionKeyName(),
                    (string) $key->getPermissionKeyDescription()
                );
            }
        }

        return $result;
    }
}
