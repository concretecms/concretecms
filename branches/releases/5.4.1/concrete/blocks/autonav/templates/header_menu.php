<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
	$aBlocks = $controller->generateNav();
	$c = Page::getCurrentPage();
	echo("<ul class=\"nav-header\">");

	$nh = Loader::helper('navigation');

	$isFirst = true;
	foreach($aBlocks as $ni) {
		$_c = $ni->getCollectionObject();
		if (!$_c->getCollectionAttributeValue('exclude_nav')) {

			$target = $ni->getTarget();
			if ($target != '') {
				$target = 'target="' . $target . '"';
			}

			if ($ni->isActive($c) || strpos($c->getCollectionPath(), $_c->getCollectionPath()) === 0) {
				$navSelected='nav-selected';
			} else {
				$navSelected = '';
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

			if ($isFirst) $isFirstClass = 'first';
			else $isFirstClass = '';
			
			echo '<li class="'.$navSelected.' '.$isFirstClass.'">';
			
			if ($c->getCollectionID() == $_c->getCollectionID()) { 
				echo('<a class="nav-selected" href="' . $pageLink . '"  ' . $target . '>' . $ni->getName() . '</a>');
			} else {
				echo('<a href="' . $pageLink . '"  ' . $target . '>' . $ni->getName() . '</a>');
			}	
			
			echo('</li>');
			$isFirst = false;			
		}
	}
	
	echo('</ul>');
	echo('<div class="ccm-spacer">&nbsp;</div>');
?>