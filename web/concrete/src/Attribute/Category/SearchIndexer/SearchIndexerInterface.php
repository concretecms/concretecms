<?php
namespace Concrete\Core\Attribute\Category\SearchIndexer;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;

interface SearchIndexerInterface
{
    public function createRepository(CategoryInterface $category);
    public function updateRepository(CategoryInterface $category, AttributeKeyInterface $key, $previousHandle = null);
    public function indexEntry(CategoryInterface $category, $mixed);
}
