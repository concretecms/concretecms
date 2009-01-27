<?
	defined('C5_EXECUTE') or die(_("Access Denied."));
?>
<a href="<?= View::url('/download_file', $bID) ?>"><?= stripslashes($controller->getLinkText()) ?></a>
 
