<?php
namespace Concrete\Core\Attribute;

/**
 * Interface that any attribute key must implement.
 */
interface AttributeKeyInterface
{
    /**
     * Get the attribute key identifier.
     *
     * @return int
     */
    public function getAttributeKeyID();

    /**
     * Get the attribute key handle.
     *
     * @return string
     */
    public function getAttributeKeyHandle();

    /**
     * Get the attribute key type.
     *
     * @return \Concrete\Core\Entity\Attribute\Type
     */
    public function getAttributeType();

    /**
     * Is the attribute key searchable?
     *
     * @return bool
     */
    public function isAttributeKeySearchable();

    /**
     * Get the search indexer.
     *
     * @return \Concrete\Core\Attribute\Key\SearchIndexer\SearchIndexerInterface
     */
    public function getSearchIndexer();

    /**
     * Get the attribute key conteoller.
     *
     * @return \Concrete\Core\Attribute\Controller
     */
    public function getController();
}
