<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
if (is_object($c)) {
	//print 'x';
	// first we check to see if emergency global caching is on
	
	
	// now we check to see if emergency caching for the page is on
	
	
	// now we just do the standard block-based full content caching
	$blocks = $c->getBlocks();
	if ($c->testBlocksForPageCache($blocks)) {

	}
}