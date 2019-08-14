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
     * @since 8.2.0
     */
    public function getName();

    /**
     * Get the fields belonging to this group.
     *
     * @return FieldInterface[]
     * @since 8.2.0
     */
    public function getFields();
}
