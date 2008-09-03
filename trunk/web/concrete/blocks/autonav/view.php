<?
	$aBlocks = $controller->generateNav();
	if (!is_object($c)) {
		$c = $b->getBlockCollectionObject();
	}
	echo("<ul class=\"nav\">");
	foreach($aBlocks as $ni) {
		$_c = $ni->getCollectionObject();
		if (!$_c->getCollectionAttributeValue('exclude_nav')) {
			
			$thisLevel = $ni->getLevel();
			if ($thisLevel > $lastLevel) {
				echo("<ul>");
			} else if ($thisLevel < $lastLevel) {
				for ($j = $thisLevel; $j < $lastLevel; $j++) {
					echo("</li></ul></li>");
				}
			} else if ($i > 0) {
				print '</li>';
			}
			
			if ($c->getCollectionID() == $_c->getCollectionID()) { 
				echo('<li class="nav-selected"><a class="nav-selected" href="' . $ni->getURL() . '">' . $ni->getName() . '</a>');
			} else {
				echo('<li><a href="' . $ni->getURL() . '">' . $ni->getName() . '</a>');
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