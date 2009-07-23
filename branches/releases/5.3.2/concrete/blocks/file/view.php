<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$f = $controller->getFileObject();
	$fp = new Permissions($f);
	if ($fp->canRead()) { 
?>
<a href="<?php echo  View::url('/download_file', $controller->getFileID()) ?>"><?php echo  stripslashes($controller->getLinkText()) ?></a>
 
<?php 
}
/*
$fo = $this->controller->getFileObject();?>
<a href="<?php echo $fo->getRelativePath()?>"><?php echo  stripslashes($controller->getLinkText()) ?></a>
*/ 
?>
