<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Type;
use Symfony\Component\HttpFoundation\Request;

/**
 * The interface that any attribute category must implement.
 */
interface CategoryInterface
{
    /**
     * Get an attribute key given its ID.
     *
     * @param int $akID
     *
     * @return \Concrete\Core\Attribute\AttributeKeyInterface|null
     */
    public function getAttributeKeyByID($akID);

    /**
     * Get an attribute key given its handle.
     *
     * @param string $akHandle
     *
     * @return \Concrete\Core\Attribute\AttributeKeyInterface|null
     */
    public function getAttributeKeyByHandle($akHandle);

    /**
     * Get all the attribute keys.
     *
     * @return \Concrete\Core\Attribute\AttributeKeyInterface[]
     */
    public function getList();

    /**
     * Get the set manager.
     *
     * @return \Concrete\Core\Attribute\SetManagerInterface
     */
    public function getSetManager();

    /**
     * Get the attribute types.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|Type[]
     */
    public function getAttributeTypes();

    /**
     * Get the indexer instance that manages search indexing (if the attribute category supports indexing).
     *
     * @return \Concrete\Core\Attribute\Category\SearchIndexer\SearchIndexerInterface|null
     */
    public function getSearchIndexer();

    /**
     * Create a new attribute key starting from the data contained in a request.
     *
     * @param Type $type The attribute type to be created
     * @param Request $request The request instance that contains the data
     *
     * @return Key
     */
    public function addFromRequest(Type $type, Request $request);

    /**
     * Update an existing attribute key with the data contained in a request.
     *
     * @param Key $key The attribute key to be updated
     * @param Request $request  The request instance that contains the data
     *
     * @return Key
     */
    public function updateFromRequest(Key $key, Request $request);

    /**
     * Get all the generic attribute values for an object instance.
     *
     * @param \Concrete\Core\Attribute\ObjectInterface $object
     *
     * @return AttributeValueInterface[]
     */
    public function getAttributeValues($object);

    /**
     * Get the generic attribute value of an object for a specific key.
     *
     * @param Key $key
     * @param \Concrete\Core\Attribute\ObjectInterface $object
     *
     * @return AttributeValueInterface|null
     */
    public function getAttributeValue(Key $key, $object);

    /**
     * Method called when a key is deleted. This is usually used to delete all the values associated to an attribute key.
     *
     * Note: this does NOT delete the source key entity. That is done simply by removing the key through Doctrine.
     * Doctrine then calls the Concrete\Core\Attribute\Key\Listener::preRemove method, which runs this.
     *
     * @param Key $key
     */
    public function deleteKey(Key $key);

    /**
     * Delete an attribute value.
     *
     * @param AttributeValueInterface $value
     */
    public function deleteValue(AttributeValueInterface $value);

    /**
     * Delete this category and all the associated attribute keys.
     */
    public function delete();
}
