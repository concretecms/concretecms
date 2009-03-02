<?
	defined('C5_EXECUTE') or die(_("Access Denied."));
?>

<a href="<?= View::url('/download_file', $controller->getFileID()) ?>"><?= stripslashes($controller->getLinkText()) ?></a>
 
<?
/*
$fo = $this->controller->getFileObject();?>
<a href="<?=$fo->getRelativePath()?>"><?= stripslashes($controller->getLinkText()) ?></a>
*/ 
?>
