<?php

namespace Concrete\Core\Attribute\Key\SearchIndexer;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;

/**
 * Interface that all the classes that handle the search index of attribute keys must implement.
 */
interface SearchIndexerInterface
{
    /**
     * Create or update the column that contains the indexed data of a specific attribute.
     *
     * @param \Concrete\Core\Attribute\Category\CategoryInterface $category
     * @param \Concrete\Core\Attribute\AttributeKeyInterface $key
     * @param string|null $previousHandle
     */
    public function updateSearchIndexKeyColumns(CategoryInterface $category, AttributeKeyInterface $key, $previousHandle);

    /**
     * Store in the index table the value of an attribute of an item.
     *
     * @param \Concrete\Core\Attribute\Category\CategoryInterface $category
     * @param \Concrete\Core\Attribute\AttributeValueInterface $value
     * @param object $subject The item owning the attribute value
     */
    public function indexEntry(CategoryInterface $category, AttributeValueInterface $value, $subject);

    /**
     * Remove from the index table the value of an attribute of an item.
     *
     * @param \Concrete\Core\Attribute\Category\CategoryInterface $category
     * @param \Concrete\Core\Attribute\AttributeValueInterface $value
     * @param object $subject The item owning the attribute value
     */
    public function clearIndexEntry(CategoryInterface $category, AttributeValueInterface $value, $subject);
}
