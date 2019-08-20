<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\File\File;
use Concrete\Core\Legacy\Loader;
use Concrete\Core\Permission\Checker as Permissions;

if ($_REQUEST['fID'] > 0) {
    $f = File::getByID($_REQUEST['fID']);
    $fp = new Permissions($f);
    if ($fp->canEditFilePermissions()) {
        Loader::element('permission/details/file', array("f" => $f));
    }
}
