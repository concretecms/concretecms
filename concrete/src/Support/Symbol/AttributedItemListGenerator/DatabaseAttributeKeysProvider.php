<?php

declare(strict_types=1);

namespace Concrete\Core\Support\Symbol\AttributedItemListGenerator;

use Concrete\Core\Entity\Attribute\Key\Key;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Read the attribute keys from the database (for installed instances).
 */
final class DatabaseAttributeKeysProvider implements AttributeKeysProviderInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var array<string, \Concrete\Core\Support\Symbol\AttributedItemListGenerator\AttributeKey[]>|null
     */
    private $keys;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Support\Symbol\AttributedItemListGenerator\AttributeKeysProviderInterface::getCategoryHandles()
     */
    public function getCategoryHandles(): array
    {
        return array_keys($this->getKeysByCategory());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Support\Symbol\AttributedItemListGenerator\AttributeKeysProviderInterface::getKeys()
     */
    public function getKeys(string $categoryHandle): array
    {
        return $this->getKeysByCategory()[$categoryHandle] ?? [];
    }

    /**
     * @return array<string, \Concrete\Core\Support\Symbol\AttributedItemListGenerator\AttributeKey[]>
     */
    private function getKeysByCategory(): array
    {
        if ($this->keys === null) {
            $keys = [];
            $repository = $this->entityManager->getRepository(Key::class);
            foreach ($repository->findBy([], ['akHandle' => 'ASC']) as $key) {
                $category = $key->getAttributeCategoryEntity();
                $categoryHandle = $category === null ? '' : (string) $category->getAttributeKeyCategoryHandle();
                $handle = (string) $key->getAttributeKeyHandle();
                if ($categoryHandle === '' || $handle === '') {
                    continue;
                }
                // The same handle may exist more than once in a category (for example the Express attribute keys of different entities)
                if (!isset($keys[$categoryHandle][$handle])) {
                    $keys[$categoryHandle][$handle] = new AttributeKey($categoryHandle, $handle, (string) $key->getAttributeKeyName(), (bool) $key->isAttributeKeySearchable());
                }
            }
            ksort($keys, SORT_STRING);
            $this->keys = array_map('array_values', $keys);
        }

        return $this->keys;
    }
}
