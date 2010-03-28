<?php  
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$aBlocks = $controller->generateNav();
	$c = Page::getCurrentPage();
	$i = 0;
	foreach($aBlocks as $ni) {
		$_c = $ni->getCollectionObject();
		if (!$_c->getCollectionAttributeValue('exclude_nav')) {	
			if ($i > 0) {
				print ' <span class="ccm-autonav-breadcrumb-sep">&gt;</span> ';
			}
			if ($c->getCollectionID() == $_c->getCollectionID()) { 
				echo($ni->getName());
			} else {
				echo('<a href="' . $ni->getURL() . '">' . $ni->getName() . '</a>');
			}	
			$lastLevel = $thisLevel;
			$i++;
		}
	}
	
	$thisLevel = 0;