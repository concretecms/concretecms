<?
	defined('C5_EXECUTE') or die("Access Denied.");

$b = Block::getByID($bOriginalID);
$b->display();