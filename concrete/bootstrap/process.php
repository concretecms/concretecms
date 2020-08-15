<?php

defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Workflow\Request\UnapprovePageRequest;

# Filename: _process.php
# Author: Andrew Embler (andrew@concrete5.org)
# -------------------
# _process.php is included at the top of the dispatcher and basically
# checks to see if a any submits are taking place. If they are, then
# _process makes sure that they're handled correctly

// if we don't have a valid token we die

// ATTENTION! This file is legacy and needs to die. We are moving it's various pieces into
// controllers.
$valt = Loader::helper('validation/token');

// If the user has checked out something for editing, we'll increment the lastedit variable within the database
$u = Core::make(Concrete\Core\User\User::class);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $u->refreshCollectionEdit($c);
}

if (isset($_REQUEST['ctask']) && $_REQUEST['ctask'] && $valt->validate()) {
    switch ($_REQUEST['ctask']) {
        case 'publish-now':
            if ($cp->canApprovePageVersions()) {
                $v = CollectionVersion::get($c, "SCHEDULED");
                $v->approve(false, null);

                header('Location: ' . \Core::getApplicationURL() . '/' . DISPATCHER_FILENAME .
                    '?cID=' . $c->getCollectionID());

                exit;
            }
            break;

        case 'cancel-schedule':
            if ($cp->canApprovePageVersions()) {
                $u = new User();
                $pkr = new UnapprovePageRequest();
                $pkr->setRequestedPage($c);
                $v = CollectionVersion::get($c, "SCHEDULED");
                $v->setPublishInterval(null, null);
                $pkr->setRequestedVersionID($v->getVersionID());
                $pkr->setRequesterUserID($u->getUserID());
                $response = $pkr->trigger();
                header(
                    'Location: ' . \Core::getApplicationURL() . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
                exit;
            }
    }
}
