<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Legacy\Loader;
use Concrete\Core\Legacy\TaskPermission;

$tp = new TaskPermission();
if ($tp->canAccessTaskPermissions()) {
    Loader::element('permission/details/block_type');
}
