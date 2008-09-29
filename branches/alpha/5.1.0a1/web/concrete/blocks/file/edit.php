<?
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$al = Loader::helper('concrete/asset_library');
	$bf = null;
	if ($controller->getFileID() > 0) { 
		$bf = $controller->getFileObject();
	}
?>
<h2>File</h2>
<?=$al->file('ccm-b-file', 'fID', 'Choose File', $bf);?>

<br/>
<h2>Link Text</h2>
<input type="text" style="width: 200px" name="fileLinkText" value="<?=$controller->getLinkText()?>" /><br/>

<h2>Password Required for Downloading</h2>
<input type="text" style="width: 200px" name="filePassword" value="<?=$controller->getPassword()?>" />
<div class="ccm-note">A password is not required.</div>