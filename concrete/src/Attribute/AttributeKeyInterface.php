<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Attribute\Key\SearchIndexer\SearchIndexerInterface;

interface AttributeKeyInterface
{
    /**
     * @return int
     */
    public function getAttributeKeyID();

    /**
     * @return string
     */
    public function getAttributeKeyHandle();

    /**
     * @return \Concrete\Core\Entity\Attribute\Type
     */
    public function getAttributeType();

    /**
     * @return bool
     */
    public function isAttributeKeySearchable();

    /**
     * @return SearchIndexerInterface
     */
    public function getSearchIndexer();

    /**
     * @return Controller
     */
    public function getController();
}
