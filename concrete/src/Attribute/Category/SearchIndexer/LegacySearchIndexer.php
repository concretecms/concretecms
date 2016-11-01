<?php
namespace Concrete\Core\Attribute\Category\SearchIndexer;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Value\Value;
use Doctrine\DBAL\Schema\Schema;

class LegacySearchIndexer extends \Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexer
{

    public function indexEntry(CategoryInterface $category, AttributeValueInterface $value, $subject)
    {
        return false; // happens in the deprecated saveAttributeForm method
    }

}
