<?php

defined('C5_EXECUTE') or die("Access Denied.");

$checker = new \Concrete\Core\Permission\Checker($calendar);
if ($checker->canEditCalendarPermissions()) {
    Loader::element('permission/details/calendar', array('calendar' => $calendar));
} else {
    echo t('You do not have permission to edit this calendar');
}
