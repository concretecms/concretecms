<?

	defined('C5_EXECUTE') or die(_("Access Denied."));

	// now that we're in the specialized content file for this block type, 
	// we'll include this block type's class, and pass the block to it, and get
	// the content
	
	// so that they can add links and what-not
	$content = $controller->getContent();
	print $content;
	
?>