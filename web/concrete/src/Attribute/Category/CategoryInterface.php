<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\SearchIndexer\SearchIndexerInterface;
use Concrete\Core\Attribute\EntityInterface;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Entity\Attribute\Key\Key;
use Symfony\Component\HttpFoundation\Request;

interface CategoryInterface
{
    /**
     * @return Category
     */
    public function getCategoryEntity();
    public function setEntity(EntityInterface $entity);
    public function setCategoryEntity(Category $entity);

    public function getAttributeKeyByID($akID);
    public function createAttributeKey();

    public function allowAttributeSets();
    public function getAttributeTypes();
    public function getAttributeSets();
    public function associateAttributeKeyType(\Concrete\Core\Entity\Attribute\Type $type);

    public function getUnassignedAttributeKeys();

    /**
     * @return SearchIndexerInterface
     */
    public function getSearchIndexer();

    public function addFromRequest(\Concrete\Core\Entity\Attribute\Type $type, Request $request);
    public function updateFromRequest(Key $key, Request $request);

    public function getAttributeValues($mixed);
    public function getAttributeValue(Key $key, $mixed);

    public function deleteKey(Key $key);
    public function deleteValue(AttributeValueInterface $value);
}
