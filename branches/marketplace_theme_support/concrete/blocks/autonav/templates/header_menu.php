<?
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$aBlocks = $controller->generateNav();
	global $c;
	echo("<ul class=\"nav-header\">");
	$isFirst = true;
	foreach($aBlocks as $ni) {
		$_c = $ni->getCollectionObject();
		if (!$_c->getCollectionAttributeValue('exclude_nav')) {
		
			if (strpos($c->getCollectionPath(), $_c->getCollectionPath()) === 0) {
				$navSelected='nav-selected';
			} else {
				$navSelected = '';
			}
			
			if ($isFirst) $isFirstClass = 'first';
			else $isFirstClass = '';
			
			echo '<li class="'.$navSelected.' '.$isFirstClass.'">';
			
			if ($c->getCollectionID() == $_c->getCollectionID()) { 
				echo('<a class="nav-selected" href="' . $ni->getURL() . '">' . $ni->getName() . '</a>');
			} else {
				echo('<a href="' . $ni->getURL() . '">' . $ni->getName() . '</a>');
			}	
			
			echo('</li>');
			$isFirst = false;			
		}
	}
	
	echo('</ul>');
	echo('<div class="ccm-spacer">&nbsp;</div>');
?>