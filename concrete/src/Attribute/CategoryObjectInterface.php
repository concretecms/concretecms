<?php

namespace Concrete\Core\Attribute;

/**
 * Interface that category objects (for instance, Express entities) must implement.
 */
interface CategoryObjectInterface
{
    /**
     * Get the attribute key category.
     *
     * @return \Concrete\Core\Attribute\Category\CategoryInterface
     */
    public function getAttributeKeyCategory();
}
