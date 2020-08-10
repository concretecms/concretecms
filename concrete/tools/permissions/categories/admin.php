<?php

use Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Duration as PermissionDuration;

defined('C5_EXECUTE') or die('Access Denied.');

$token = app('token');

$p = new Checker();
if ($p->canAccessTaskPermissions()) {
    switch ($_REQUEST['task'] ?? '') {
        case 'add_access_entity':
            if ($token->validate('add_access_entity')) {
                $pk = PermissionKey::getByID($_REQUEST['pkID']);
                $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
                $pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
                $pd = PermissionDuration::getByID($_REQUEST['pdID']);
                $pa->addListItem($pe, $pd, $_REQUEST['accessType']);
            }
            break;
        case 'remove_access_entity':
            if ($token->validate('remove_access_entity')) {
                $pk = PermissionKey::getByID($_REQUEST['pkID']);
                $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
                $pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
                $pa->removeListItem($pe);
            }
            break;
        case 'save_permission':
            if ($token->validate('save_permission')) {
                $pk = PermissionKey::getByID($_REQUEST['pkID']);
                $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
                $pa->save($_POST);
            }
            break;
        case 'display_access_cell':
            if ($token->validate('display_access_cell')) {
                $pk = PermissionKey::getByID($_REQUEST['pkID']);
                $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
                Loader::element('permission/labels', ['pk' => $pk, 'pa' => $pa]);
            }
            break;
    }
}
