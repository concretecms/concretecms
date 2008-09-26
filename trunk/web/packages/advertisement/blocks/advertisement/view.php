<?

defined('C5_EXECUTE') or die(_("Access Denied."));

$b = $controller->getBlockObject();
$btID = $b->getBlockTypeID();
$bt = BlockType::getByID($btID);
$uh = Loader::helper('concrete/urls');

if($controller->ad->aID > 0) { // Display single ad
	echo "<div class=\"advertisement\">";
	$controller->generateImpression($c);	
	if(strlen(trim($controller->ad->html)) > 5) {
		echo $controller->ad->html;
	} else { 
		echo '<a href="' . $uh->getBlockTypeToolsURL($bt) . '/ad_click.php?aID=' . $controller->ad->aID.'" target="_blank">' . 
			$controller->getContentAndGenerate($b->getBlockName(), '', $extraParams['imageAlign']) . '</a>';
	}
	echo "</div>";
} elseif ($controller->agID > 0) { // Display group of ads
	
	$db = Loader::db();
	$aIDs = $db->getCol("SELECT aID FROM btAdvertisementToGroups WHERE agID = ?",array($controller->agID));
	if(is_array($aIDs) && count($aIDs)) {
		$adArray = $controller->ad->Find("aID IN (".implode(',',$aIDs).") ORDER BY RAND() ".($controller->numAds?"LIMIT ".$controller->numAds:""));
		
		foreach($adArray as $adDetail) {
			echo "<div class=\"advertisement\">";
			$adDetail->generateImpression();	
			if(strlen(trim($adDetail->html)) > 5) {
				echo($adDetail->html);
			} else { 
				echo '<a href="' . $uh->getBlockTypeToolsURL($bt) . '/ad_click.php?aID=' . $adDetail->aID.'" target="_blank">' . 
				$adDetail->getContentAndGenerate($adDetail->name, '', $extraParams['imageAlign']) . '</a>';
			}	
			echo "</div>";
		}
	}	
} ?>
