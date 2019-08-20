<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Legacy\Loader;
use Concrete\Core\Permission\Checker as Permissions;

$p = new Permissions();
if ($p->canAccessTaskPermissions()) {
    Loader::element('permission/details/miscellaneous');
}
