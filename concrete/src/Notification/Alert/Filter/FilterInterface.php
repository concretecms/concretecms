<?php
namespace Concrete\Core\Notification\Alert\Filter;

use Concrete\Core\Notification\Alert\AlertList;

interface FilterInterface
{

    /**
     * Get the human readable name of this filter
     *
     * @return string
     */
    public function getName();

    /**
     * Get the key of this filter
     *
     * @return string
     */
    public function getKey();

    /**
     * Apply this filter to a given alertlist
     *
     * @param AlertList $list
     *
     * @return void
     */
    public function filterAlertList(AlertList $list);

}
