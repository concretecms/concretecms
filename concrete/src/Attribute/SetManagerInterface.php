<?php
namespace Concrete\Core\Attribute;

/**
 * Handles adding and removing keys from attribute sets.
 */
interface SetManagerInterface
{
    /**
     * Get one of the \Concrete\Core\Attribute\StandardSetManager::ASET_ALLOW_... constants.
     *
     * @return int
     */
    public function allowAttributeSets();

    /**
     * Get the attribute sets.
     *
     * @return \Concrete\Core\Entity\Attribute\Set[]
     */
    public function getAttributeSets();

    /**
     * Get the attribute keys that are not in any set.
     *
     * @return \Concrete\Core\Attribute\AttributeKeyInterface
     */
    public function getUnassignedAttributeKeys();

    /**
     * Update the order of the attribute sets.
     *
     * @param int[] $attributeSetIdentifiers the sorted list of the attribute set identifiers
     */
    public function updateAttributeSetDisplayOrder($attributeSetIdentifiers);
}
