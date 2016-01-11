<?php
namespace Concrete\Core\Attribute\Category\SearchIndexer;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;

interface SearchIndexerInterface
{
    public function createTable(CategoryInterface $category);
    public function updateTable(CategoryInterface $category, AttributeKeyInterface $key, $previousHandle = null);
    public function indexEntry(CategoryInterface $category, $mixed);
}
