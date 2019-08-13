<?php

namespace Concrete\Core\Attribute;

/**
 * Interface that category objects (for instance, Express entities) must implement.
 * @since 8.0.0
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
