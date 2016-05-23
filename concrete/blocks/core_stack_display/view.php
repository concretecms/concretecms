<?php
defined('C5_EXECUTE') or die('Access Denied.');

$c = Page::getCurrentPage();
$cp = new Permissions($c);
if ($cp->canViewPageVersions()) {
    $stack = Stack::getByID($stID);
} else {
    $stack = Stack::getByID($stID, 'ACTIVE');
}
if ($stack) {
    $ax = Area::get($stack, STACKS_AREA_NAME);
    $axp = new Permissions($ax);
    if ($axp->canRead()) {
        $ax->disableControls();
        $ax->display($stack);
    }
}
