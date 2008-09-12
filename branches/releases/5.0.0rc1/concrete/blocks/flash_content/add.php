<?php  
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
?>
<h2>Flash (swf) File</h2>
<?php echo $al->file('ccm-b-file', 'fID', 'Choose File');?>

<br/>
<h2>Quality</h2>
<select name="quality">
	<option value="low">low</option>
    <option value="autolow">autolow</option>
    <option value="autohigh">autohigh</option>
    <option value="medium">medium</option>
    <option value="high" selected="selected">high</option>
    <option value="best">best</option>
</select><br /><br />

<h2>Minimum Flash Player Version</h2>
<input type="text" name="minVersion" value="8.0" /><br /><br />