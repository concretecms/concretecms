<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
?>
<a href="<?php echo  View::url('/download_file', $bID) ?>"><?php echo  stripslashes($controller->getLinkText()) ?></a>
 
