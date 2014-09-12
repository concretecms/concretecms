<?php
use Concrete\Core\Block\View\BlockView;

defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getByID($_REQUEST['cID'], "ACTIVE");

$bt = BlockType::getByHandle('autonav');
$bt->controller->collection = $c;
$bt->controller->orderBy = $_REQUEST['orderBy'];
$bt->controller->cID = $_REQUEST['cID'];
$bt->controller->displayPages = $_REQUEST['displayPages'];
$bt->controller->displaySubPages = $_REQUEST['displaySubPages'];
$bt->controller->displaySubPageLevels = $_REQUEST['displaySubPageLevels'];
$bt->controller->displaySubPageLevelsNum = $_REQUEST['displaySubPageLevelsNum'];
$bt->controller->displayUnavailablePages = $_REQUEST['displayUnavailablePages'];

if ($bt->controller->displayPages == "custom") {
    $bt->controller->displayPagesCID = $_REQUEST['displayPagesCID'];
    $bt->controller->displayPagesIncludeSelf = $_REQUEST['displayPagesIncludeSelf'];
}

$bv = new BlockView($bt);
$bv->render('view');
exit;
