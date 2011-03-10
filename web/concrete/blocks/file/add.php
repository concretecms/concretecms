<? 
defined('C5_EXECUTE') or die("Access Denied.");
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
?>
<h2><?=t('File')?></h2>
<?=$al->file('ccm-b-file', 'fID', t('Choose File'));?>

<br/>
<h2><?=t('Link Text')?></h2>
<input type="text" style="width: 200px" name="fileLinkText" /><br />