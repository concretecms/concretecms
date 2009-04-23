<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$aBlocks = $controller->generateNav();
	global $c;
	echo("<ul class=\"nav\">");
	
	$nh = Loader::helper('navigation');
	
	foreach($aBlocks as $ni) {
		$_c = $ni->getCollectionObject();
		if (!$_c->getCollectionAttributeValue('exclude_nav')) {
			
			$thisLevel = $ni->getLevel();
			if ($thisLevel > $lastLevel) {
				echo("<ul>");
			} else if ($thisLevel < $lastLevel) {
				for ($j = $thisLevel; $j < $lastLevel; $j++) {
					if ($lastLevel - $j > 1) {
						echo("</li></ul>");
					} else {
						echo("</li></ul></li>");
					}
				}
			} else if ($i > 0) {
				echo("</li>");
			}

			$pageLink = false;
			
			if ($_c->getCollectionAttributeValue('replace_link_with_first_in_nav')) {
				$subPage = $_c->getFirstChild();
				if ($subPage instanceof Page) {
					$pageLink = $nh->getLinkToCollection($subPage);
				}
			}
			
			if (!$pageLink) {
				$pageLink = $ni->getURL();
			}

			if ($c->getCollectionID() == $_c->getCollectionID()) { 
				echo('<li class="nav-selected"><a class="nav-selected" href="' . $pageLink . '">' . $ni->getName() . '</a>');
			} else {
				echo('<li><a href="' . $pageLink . '">' . $ni->getName() . '</a>');
			}	
			$lastLevel = $thisLevel;
			$i++;
			
			
		}
	}
	
	$thisLevel = 0;
	for ($i = $thisLevel; $i <= $lastLevel; $i++) {
		echo("</li></ul>");
	}

?>