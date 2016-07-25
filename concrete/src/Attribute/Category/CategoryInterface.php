<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\SearchIndexer\SearchIndexerInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Symfony\Component\HttpFoundation\Request;

interface CategoryInterface
{
    public function getAttributeKeyByID($akID);
    public function getList();
    public function getSetManager();
    public function getAttributeTypes();

    /**
     * @return SearchIndexerInterface|null
     */
    public function getSearchIndexer();

    public function addFromRequest(\Concrete\Core\Entity\Attribute\Type $type, Request $request);
    public function updateFromRequest(Key $key, Request $request);

    public function getAttributeValues($mixed);
    public function getAttributeValue(Key $key, $mixed);

    public function deleteKey(Key $key);
    public function deleteValue(AttributeValueInterface $value);
    public function delete();

}
