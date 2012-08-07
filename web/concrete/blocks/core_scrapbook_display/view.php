<?
	defined('C5_EXECUTE') or die("Access Denied.");

$area = $b->getBlockAreaObject();
$_bx = Block::getByID($bOriginalID);
$_bx->setBlockAreaObject($area);
$c = Page::getCurrentPage();
$_bx->setProxyBlock($b);
$_bx->loadNewCollection($c);
$_bx->display();