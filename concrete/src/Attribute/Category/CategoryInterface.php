<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\SearchIndexer\SearchIndexerInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Symfony\Component\HttpFoundation\Request;

interface CategoryInterface
{
    public function getAttributeKeyByID($akID);
    public function getAttributeKeyByHandle($akHandle);
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

    /**
     * Run when a key is deleted. Note this does NOT delete the source key entity. That is done simply by removing
     * the key through Doctrine. Doctrine then calls the Concrete\Core\Attribute\Key\Listener::preRemove method,
     * which runs this.
     * @param Key $key
     * @return mixed
     */
    public function deleteKey(Key $key);
    public function deleteValue(AttributeValueInterface $value);
    public function delete();

}
