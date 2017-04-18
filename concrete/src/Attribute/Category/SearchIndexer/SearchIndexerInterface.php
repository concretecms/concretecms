<?php
namespace Concrete\Core\Attribute\Category\SearchIndexer;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Entity\Attribute\Value\Value;

interface SearchIndexerInterface
{
    /**
     * @param CategoryInterface $category
     * @return void
     */
    public function createRepository(CategoryInterface $category);

    /**
     * @param CategoryInterface $category
     * @param AttributeKeyInterface $key
     * @param string|null $previousHandle
     * @return void
     */
    public function updateRepositoryColumns(CategoryInterface $category, AttributeKeyInterface $key, $previousHandle = null);

    /*
     * This was added in v8.2 but we can't assume that everyone has implemented this yet.
     * Uncomment this in the future when we are sure we won't break anything
     *
     * @param CategoryInterface $category
     * @param AttributeKeyInterface $key
     * @return void
     */
//     public function refreshRepositoryColumns(CategoryInterface $category, AttributeKeyInterface $key);

    /**
     * @param CategoryInterface $category
     * @param AttributeValueInterface $attributeValue
     * @param mixed $subject
     * @return void
     */
    public function indexEntry(CategoryInterface $category, AttributeValueInterface $attributeValue, $subject);

    /**
     * @param CategoryInterface $category
     * @param AttributeValueInterface $attributeValue
     * @param mixed $subject
     * @return void
     */
    public function clearIndexEntry(CategoryInterface $category, AttributeValueInterface $attributeValue, $subject);
}
