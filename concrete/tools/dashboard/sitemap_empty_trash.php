<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Legacy\Loader;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Permission\Key\Key as PermissionKey;

$pk = PermissionKey::getByHandle('empty_trash');
if (!$pk->validate()) {
    die(t("Access Denied."));
}

$trash = Page::getByPath(Config::get('concrete.paths.trash'));
$i = 0;
if (is_object($trash) && !$trash->isError()) {
    $pl = new PageList();
    $pl->filterByParentID($trash->getCollectionID());
    $pl->includeInactivePages();
    $pl->setPageVersionToRetrieve(\Concrete\Core\Page\PageList::PAGE_VERSION_RECENT);
    $pages = $pl->getResults();
    foreach ($pages as $pc) {
        $cp = new Permissions($pc);
        if ($cp->canDeletePage()) {
            ++$i;
            $pc->delete();
        }
    }
}

$message = t2('%d page deleted.', '%d pages deleted.', $i, $i);

$obj = new stdClass();
$obj->message = $message;
echo Loader::helper('json')->encode($obj);
