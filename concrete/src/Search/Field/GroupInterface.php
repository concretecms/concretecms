<?php
namespace Concrete\Core\Search\Field;

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
