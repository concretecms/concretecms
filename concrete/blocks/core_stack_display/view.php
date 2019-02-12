<?php

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Checker;

defined('C5_EXECUTE') or die('Access Denied.');

/* @var int $stID */

$c = Page::getCurrentPage();
$cp = new Checker($c);
if ($cp->canViewPageVersions()) {
    $stack = Stack::getByID($stID);
} else {
    $stack = Stack::getByID($stID, 'ACTIVE');
}
if ($stack) {
    $axp = new Checker($stack);
    if ($axp->canRead()) {
        $ax = Area::get($stack, STACKS_AREA_NAME);
        $ax->disableControls();
        $ax->display($stack);
    }
}
