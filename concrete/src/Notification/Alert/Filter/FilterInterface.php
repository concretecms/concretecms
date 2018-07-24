<?php
namespace Concrete\Core\Notification\Alert\Filter;

use Concrete\Core\Notification\Alert\AlertList;

interface FilterInterface
{

    public function getName();
    public function getKey();
    public function filterAlertList(AlertList $list);

}