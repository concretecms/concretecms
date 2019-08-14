<?php
namespace Concrete\Core\Search\Field;

/**
 * @since 8.0.0
 */
interface ManagerInterface
{
    /**
     * Get the groups of fields.
     *
     * @return GroupInterface[]
     * @since 8.2.0
     */
    public function getGroups();

    /**
     * Get a fields group by name.
     *
     * @param string $name
     *
     * @return GroupInterface|null
     * @since 8.2.0
     */
    public function getGroupByName($name);

    /**
     * Search a field across all the groups.
     *
     * @param string $key The field key
     *
     * @return FieldInterface|null
     * @since 8.2.0
     */
    public function getFieldByKey($key);

    /**
     * Get the list of fields whose keys are in the 'field' of an array, and initialize the fields with the data.
     *
     * @param array $request
     *
     * @return FieldInterface[]
     * @since 8.2.0
     */
    public function getFieldsFromRequest(array $request);
}
