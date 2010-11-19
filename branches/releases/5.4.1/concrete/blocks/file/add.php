<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
?>
<h2><?php echo t('File')?></h2>
<?php echo $al->file('ccm-b-file', 'fID', t('Choose File'));?>

<br/>
<h2><?php echo t('Link Text')?></h2>
<input type="text" style="width: 200px" name="fileLinkText" /><br />