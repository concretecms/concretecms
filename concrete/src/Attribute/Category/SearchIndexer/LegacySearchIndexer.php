<?php

namespace Concrete\Core\Attribute\Category\SearchIndexer;

use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;

class LegacySearchIndexer extends \Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexer
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexer::indexEntry()
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\SearchIndexerInterface::indexEntry()
     */
    public function indexEntry(CategoryInterface $category, AttributeValueInterface $value, $subject)
    {
        return false; // happens in the deprecated saveAttributeForm method
    }
}
