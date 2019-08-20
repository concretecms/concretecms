<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Legacy\Loader;
use Concrete\Core\Page\EditResponse as PageEditResponse;
use Concrete\Core\Page\Page;

$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
    die(t('Access Denied'));
}

if (isset($_REQUEST['cID']) && is_array($_REQUEST['cID'])) {
    foreach ($_REQUEST['cID'] as $displayOrder => $cID) {
        $v = array($displayOrder, $cID);
        $c = Page::getByID($cID);
        $c->updateDisplayOrder($displayOrder, $cID);
    }
}

$r = new PageEditResponse();
$r->setMessage(t("Display order saved."));
$r->outputJSON();
