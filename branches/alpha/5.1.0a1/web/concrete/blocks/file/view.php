<?
	// now that we're in the specialized content file for this block type, 
	// we'll include this block type's class, and pass the block to it, and get
	// the content
?>
<a href="<?= View::url('/download_file', $bID) ?>"><?= stripslashes($controller->getLinkText()) ?></a>
 
