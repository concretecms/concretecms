<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Legacy\Loader;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\Core\Permission\Checker as Permissions;

$ch = Page::getByPath('/dashboard/pages/types', 'RECENT');
$chp = new Permissions($ch);
if ($_REQUEST['ptID'] > 0) {
    $pt = PageType::getByID($_REQUEST['ptID']);
    $fsp = new Permissions($fs);
    if ($chp->canViewPage()) {
        Loader::element('permission/details/page_type', array("pagetype" => $pt));
    }
}
