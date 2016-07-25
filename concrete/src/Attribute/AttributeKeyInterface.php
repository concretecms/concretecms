<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Attribute\Key\SearchIndexer\SearchIndexerInterface;

interface AttributeKeyInterface
{
    public function getAttributeKeyID();
    public function getAttributeKeyHandle();
    public function getAttributeType();
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
