<?php

namespace Concrete\Core\Api\Attribute;

/**
 * Used to join a command to a set of attributes and their associated proposed attribute values entities.
 */
class AttributeValueMap
{

    /**
     * @var AttributeValueMapEntry[]
     */
    protected $entries = [];

    public function addEntry(AttributeValueMapEntry $entry)
    {
        $this->entries[$entry->getAttributeKey()->getAttributeKeyID()] = $entry;
    }

    /**
     * @return AttributeValueMapEntry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    public function containsKey(string $handle)
    {
        return array_key_exists($handle, $this->entries);
    }




}