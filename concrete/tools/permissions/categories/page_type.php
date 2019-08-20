<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Legacy\Loader;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Permission\Duration as PermissionDuration;
use Concrete\Core\Permission\Key\Key as PermissionKey;

if ($_REQUEST['ptID'] > 0) {
    $pt = PageType::getByID($_REQUEST['ptID']);
}
$ch = Page::getByPath('/dashboard/pages/types');
$chp = new Permissions($ch);
if ($chp->canViewPage()) {
    if ($_REQUEST['task'] == 'add_access_entity' && Loader::helper("validation/token")->validate('add_access_entity')) {
        $pk = PermissionKey::getByID($_REQUEST['pkID']);
        $pk->setPermissionObject($pt);
        $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
        $pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
        $pd = PermissionDuration::getByID($_REQUEST['pdID']);
        $pa->addListItem($pe, $pd, $_REQUEST['accessType']);
    }

    if ($_REQUEST['task'] == 'remove_access_entity' && Loader::helper("validation/token")->validate('remove_access_entity')) {
        $pk = PermissionKey::getByID($_REQUEST['pkID']);
        $pk->setPermissionObject($pt);
        $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
        $pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
        $pa->removeListItem($pe);
    }

    if ($_REQUEST['task'] == 'save_permission' && Loader::helper("validation/token")->validate('save_permission')) {
        $pk = PermissionKey::getByID($_REQUEST['pkID']);
        $pk->setPermissionObject($pt);
        $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
        $pa->save($_POST);
    }

    if ($_REQUEST['task'] == 'display_access_cell' && Loader::helper("validation/token")->validate('display_access_cell')) {
        $pk = PermissionKey::getByID($_REQUEST['pkID']);
        $pk->setPermissionObject($pt);
        $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
        Loader::element('permission/labels', array('pk' => $pk, 'pa' => $pa));
    }
}
