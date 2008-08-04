<? 
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
?>
<h2>File</h2>
<?=$al->file('ccm-b-file', 'fID', 'Choose File');?>

<br/>
<h2>Link Text</h2>
<input type="text" style="width: 200px" name="fileLinkText" /><br /><br />