<?php

namespace Concrete\Core\Attribute\Category\SearchIndexer;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;

/**
 * Interface that all the classes that handle the search index of attribute categories must implement.
 */
interface SearchIndexerInterface
{
    /**
     * Create the database table where the data of the indexed attributes will be stored.
     *
     * @param \Concrete\Core\Attribute\Category\CategoryInterface $category
     */
    public function createRepository(CategoryInterface $category);

    /**
     * Create or update the column that contains the indexed data of a specific attribute.
     *
     * @param \Concrete\Core\Attribute\Category\CategoryInterface $category
     * @param \Concrete\Core\Attribute\AttributeKeyInterface $key
     * @param string|null $previousHandle
     */
    public function updateRepositoryColumns(CategoryInterface $category, AttributeKeyInterface $key, $previousHandle = null);

    /**
     * Store in the index table the value of an attribute of an item.
     *
     * @param \Concrete\Core\Attribute\Category\CategoryInterface $category
     * @param \Concrete\Core\Attribute\AttributeValueInterface $attributeValue
     * @param object $subject The item owning the attribute value
     */
    public function indexEntry(CategoryInterface $category, AttributeValueInterface $attributeValue, $subject);

    /**
     * Remove from the index table the value of an attribute of an item.
     *
     * @param \Concrete\Core\Attribute\Category\CategoryInterface $category
     * @param \Concrete\Core\Attribute\AttributeValueInterface $attributeValue
     * @param object $subject The item owning the attribute value
     */
    public function clearIndexEntry(CategoryInterface $category, AttributeValueInterface $attributeValue, $subject);
}
