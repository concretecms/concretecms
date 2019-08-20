<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Legacy\Loader;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker as Permissions;

$c = Page::getByPath('/dashboard/system/permissions/users');
$cp = new Permissions($c);
if ($cp->canViewPage()) {
    Loader::element('permission/details/user');
}
