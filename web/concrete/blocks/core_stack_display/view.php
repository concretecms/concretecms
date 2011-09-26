<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$c = Page::getCurrentPage();
	$cp = new Permissions($c);
	if ($cp->canReadVersions()) {
		$stack = Stack::getByID($stID);	
	} else {
		$stack = Stack::getByID($stID, 'ACTIVE');
	}
	$pp = new Permissions($stack);
	if ($pp->canRead()) {
		$ax = new Area(STACKS_AREA_NAME);
		$ax->display($stack);
	}