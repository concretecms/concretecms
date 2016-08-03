<?php
namespace Concrete\Core\Attribute\Category\SearchIndexer;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Entity\Attribute\Value\Value;

interface SearchIndexerInterface
{
    public function createRepository(CategoryInterface $category);
    public function updateRepositoryColumns(CategoryInterface $category, AttributeKeyInterface $key, $previousHandle = null);
    public function indexEntry(CategoryInterface $category, AttributeValueInterface $attributeValue, $subject);
    public function clearIndexEntry(CategoryInterface $category, AttributeValueInterface $attributeValue, $subject);
}
