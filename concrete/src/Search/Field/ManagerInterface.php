<?php
namespace Concrete\Core\Search\Field;

interface ManagerInterface
{
    /**
     * Get the groups of fields.
     *
     * @return GroupInterface[]
     */
    public function getGroups();

    /**
     * Get a fields group by name.
     *
     * @param string $name
     *
     * @return GroupInterface|null
     */
    public function getGroupByName($name);

    /**
     * Search a field across all the groups.
     *
     * @param string $key The field key
     *
     * @return FieldInterface|null
     */
    public function getFieldByKey($key);

    /**
     * Get the list of fields whose keys are in the 'field' of an array, and initialize the fields with the data.
     *
     * @param array $request
     *
     * @return FieldInterface[]
     */
    public function getFieldsFromRequest(array $request);
}
