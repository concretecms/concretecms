<?php

namespace Concrete\Core\Search\ItemList\Database;

use Concrete\Core\Attribute\Exception\InvalidAttributeException;
use Concrete\Core\Search\StickyRequest;

abstract class AttributedItemList extends ItemList
{
    /**
     * Magic method for setting up additional filtering by attributes.
     *
     * @param $nm
     * @param $a
     *
     * @throws \Exception
     */
    public function __call($nm, $a)
    {
        if (substr($nm, 0, 8) == 'filterBy') {
            $handle = uncamelcase(substr($nm, 8));
            if (count($a) == 2) {
                $this->filterByAttribute($handle, $a[0], $a[1]);
            } else {
                $this->filterByAttribute($handle, $a[0]);
            }
        } else {
            if (substr($nm, 0, 6) == 'sortBy') {
                $handle = uncamelcase(substr($nm, 6));
                if (count($a) == 1) {
                    $this->sanitizedSortBy('ak_' . $handle, $a[0]);
                } else {
                    $this->sanitizedSortBy('ak_' . $handle);
                }
            } else {
                throw new \Exception(t('%s method does not exist for the %s class', $nm, get_called_class()));
            }
        }
    }

    /**
     * Filters by an attribute.
     *
     * @param mixed $handle
     * @param mixed $value
     * @param mixed $comparison
     *
     * @throws InvalidAttributeException
     */
    public function filterByAttribute($handle, $value, $comparison = '=')
    {
        $ak = call_user_func_array([$this->getAttributeKeyClassName(), 'getByHandle'], [$handle]);
        if (!is_object($ak)) {
            throw new InvalidAttributeException(t('Unable to find attribute %s', $handle));
        }
        $ak->getController()->filterByAttribute($this, $value, $comparison);
    }

    /**
     * @param StickyRequest $request
     */
    public function setupAutomaticSorting(StickyRequest $request = null)
    {
        // First, we check to see if there are any sortable attributes we can add to the
        // auto sort columns.
        if (is_callable([$this->getAttributeKeyClassName(), 'getList'])) {
            $l = call_user_func([$this->getAttributeKeyClassName(), 'getList']);
            foreach($l as $ak) {
                $this->autoSortColumns[] = 'ak_' . $ak->getAttributeKeyHandle();
            }
        }
    }

    abstract protected function getAttributeKeyClassName();
}
