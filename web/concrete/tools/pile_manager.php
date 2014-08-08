<?php
use Concrete\Core\Page\Stack\Pile\Pile;
use Concrete\Core\Page\Stack\Pile\PileContent;

defined('C5_EXECUTE') or die("Access Denied.");



if (!$u->isRegistered()) {
    die(t("Access Denied."));
}

$p = false;
$c = Page::getByID($_REQUEST['cID']);
// add a block to a pile
$cp = new Permissions($c);
if (!$cp->canViewPage()) {
    exit;
}

if (Request::request('task', 'default') === 'delete') {
    PileContent::get(Request::request('pcID', 0))->delete();
} else {
    $u = new User();

    $a = Area::get($c, $_REQUEST['arHandle']);
    if ($a->isGlobalArea()) {
        $ax = STACKS_AREA_NAME;
        $cx = Stack::getByName($_REQUEST['arHandle']);
    }
    $b = Block::getByID($_REQUEST['bID'], $cx, $ax);
    if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
        $bi = $b->getInstance();
        $b = Block::getByID($bi->getOriginalBlockID());
    }
    $ap = new Permissions($a);
    if (!$ap->canViewArea()) {
        exit;
    }
    $obj = & $b;

    $p = \Concrete\Core\Page\Stack\Pile\Pile::getDefault();
    $p->add($obj);
    $added = true;
}

