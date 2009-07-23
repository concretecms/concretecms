<?php  
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$aBlocks = $controller->generateNav();
	global $c;
	foreach($aBlocks as $ni) {
		$_c = $ni->getCollectionObject();
		if (!$_c->getCollectionAttributeValue('exclude_nav')) {			
			if ($c->getCollectionID() == $_c->getCollectionID()) { 
				echo($ni->getName());
			} else {
				echo('<a href="' . $ni->getURL() . '">' . $ni->getName() . '</a> <span class="ccm-autonav-breadcrumb-sep">&gt;</span> ');
			}	
			$lastLevel = $thisLevel;
			$i++;
		}
	}
	
	$thisLevel = 0;
	echo("</p>");

?>