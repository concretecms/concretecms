<?php
namespace Concrete\Core\Notification\Alert\Filter;

use Concrete\Core\Notification\Alert\AlertList;

interface FilterInterface
{

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getKey();

    /**
     * @param AlertList $list
     * @return void
     */
    public function filterAlertList(AlertList $list);

}