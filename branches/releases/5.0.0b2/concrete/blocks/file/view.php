<?

	// now that we're in the specialized content file for this block type, 
	// we'll include this block type's class, and pass the block to it, and get
	// the content
	
	$file = $controller->getFileObject();
	$b = $controller->getBlockObject();
	$btID = $b->getBlockTypeID();
	$bt = BlockType::getByID($btID);
	$uh = Loader::helper('concrete/urls');
?>
<a href="<?=$uh->getBlockTypeToolsURL($bt)."/download.php?bID=".$b->getBlockID()."&cID=".$b->getBlockCollectionID()."&arHandle=".$b->getAreaHandle()?>"><?=stripslashes($controller->getLinkText())?></a>
 