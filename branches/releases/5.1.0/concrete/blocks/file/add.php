<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
?>
<h2><?php echo t('File')?></h2>
<?php echo $al->file('ccm-b-file', 'fID', t('Choose File'));?>

<br/>
<h2><?php echo t('Link Text')?></h2>
<input type="text" style="width: 200px" name="fileLinkText" /><br />

<h2><?php echo t('Password Required for Downloading')?></h2>
<input type="text" style="width: 200px" name="filePassword" />
<div class="ccm-note"><?php echo t('A password is not required.')?></div>