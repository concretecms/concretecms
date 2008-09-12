<?php 
	$al = Loader::helper('concrete/asset_library');
	$bf = null;
	if ($controller->getFileID() > 0) { 
		$bf = $controller->getFileObject();
	}
?>
<h2>File</h2>
<?php echo $al->file('ccm-b-file', 'fID', 'Choose File', $bf);?>

<br/><br/>
<h2>Quality</h2>
<select name="quality">
	<option value="low" <?php echo ($quality == "low"?"selected=\"selected\"":"")?>>low</option>
    <option value="autolow" <?php echo ($quality == "autolow"?"selected=\"selected\"":"")?>>autolow</option>
    <option value="autohigh" <?php echo ($quality == "autohigh"?"selected=\"selected\"":"")?>>autohigh</option>
    <option value="medium" <?php echo ($quality == "medium"?"selected=\"selected\"":"")?>>medium</option>
    <option value="high" <?php echo ($quality == "high"?"selected=\"selected\"":"")?>>high</option>
    <option value="best" <?php echo ($quality == "best"?"selected=\"selected\"":"")?>>best</option>
</select><br /><br />

<h2>Minimum Flash Player Version</h2>
<input type="text" name="minVersion" value="<?php echo $minVersion?>" /><br /><br />