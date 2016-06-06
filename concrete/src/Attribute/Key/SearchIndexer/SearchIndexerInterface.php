<?php
namespace Concrete\Core\Attribute\Key\SearchIndexer;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;

interface SearchIndexerInterface
{

    function clearIndexEntry(CategoryInterface $category, AttributeValueInterface $value, $subject);
    function indexEntry(CategoryInterface $category, AttributeValueInterface $value, $subject);
    function updateSearchIndexKeyColumns(CategoryInterface $category, AttributeKeyInterface $key, $previousHandle);

}
