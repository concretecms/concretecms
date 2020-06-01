<?php

use Concrete\Core\Permission\Checker;
use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

$p = new Checker();
if ($p->canAccessTaskPermissions()) {
    View::element('permission/details/miscellaneous');
}
