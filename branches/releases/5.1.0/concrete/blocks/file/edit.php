<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$al = Loader::helper('concrete/asset_library');
	$bf = null;
	if ($controller->getFileID() > 0) { 
		$bf = $controller->getFileObject();
	}
?>
<h2><?php echo t('File')?></h2>
<?php echo $al->file('ccm-b-file', 'fID', t('Choose File'), $bf);?>

<br/>
<h2><?php echo t('Link Text')?></h2>
<input type="text" style="width: 200px" name="fileLinkText" value="<?php echo $controller->getLinkText()?>" /><br/>

<h2><?php echo t('Password Required for Downloading')?></h2>
<input type="text" style="width: 200px" name="filePassword" value="<?php echo $controller->getPassword()?>" />
<div class="ccm-note"><?php echo t('A password is not required.')?></div>