<?
	defined('C5_EXECUTE') or die("Access Denied.");

$area = $b->getBlockAreaObject();
$b = Block::getByID($bOriginalID);
$b->setBlockAreaObject($area);
$c = Page::getCurrentPage();
$b->loadNewCollection($c);
$b->display();