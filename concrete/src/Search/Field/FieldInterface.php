<?php
namespace Concrete\Core\Search\Field;

use Concrete\Core\Search\ItemList\ItemList;

interface FieldInterface extends \JsonSerializable
{
    /**
     * Get the field key.
     *
     * @return string
     */
    public function getKey();

    /**
     * Get the field display name.
     *
     * @return string
     */
    public function getDisplayName();

    /**
     * Generates the HTML for the search form (or an empty string if search is not supported).
     *
     * @return string
     */
    public function renderSearchField();

    /**
     * Apply the filter to an ItemList instance.
     *
     * @param ItemList $list
     */
    public function filterList(ItemList $list);

    /**
     * Set the search criteria.
     *
     * @param array $request
     */
    public function loadDataFromRequest(array $request);
}
