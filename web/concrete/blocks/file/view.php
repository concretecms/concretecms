<?php  defined('C5_EXECUTE') or die("Access Denied.");
	$f = $controller->getFileObject();
	$fp = new Permissions($f);
	if ($fp->canViewFile()) { 
		$c = Page::getCurrentPage();
		if($c instanceof Page) {
			$cID = $c->getCollectionID();
		}
?>
<a href="<?= View::url('/download_file', $controller->getFileID(),$cID) ?>"><?= stripslashes($controller->getLinkText()) ?></a>
 
<?
}
/*
$fo = $this->controller->getFileObject();?>
<a href="<?=$fo->getRelativePath()?>"><?= stripslashes($controller->getLinkText()) ?></a>
*/ 
?>
