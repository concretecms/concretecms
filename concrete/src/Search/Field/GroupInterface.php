<?php
namespace Concrete\Core\Search\Field;

/**
 * @since 8.0.0
 */
interface GroupInterface
{
    /**
     * Get the group name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the fields belonging to this group.
     *
     * @return FieldInterface[]
     */
    public function getFields();
}
