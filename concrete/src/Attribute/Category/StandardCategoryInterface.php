<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\SearchIndexer\SearchIndexerInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Symfony\Component\HttpFoundation\Request;

interface StandardCategoryInterface
{

    function getCategoryEntity();

}
