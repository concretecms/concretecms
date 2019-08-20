<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\File\Set\Set as FileSet;
use Concrete\Core\Legacy\Loader;
use Concrete\Core\Permission\Checker as Permissions;

if ($_REQUEST['fsID'] > 0) {
    $fs = FileSet::getByID($_REQUEST['fsID']);
} else {
    $fs = FileSet::getGlobal();
}
$fsp = new Permissions($fs);
if ($fsp->canEditFileSetPermissions()) {
    Loader::element('permission/details/file_set', array("fileset" => $fs));
}
