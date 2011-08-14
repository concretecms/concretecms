<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$al = Loader::helper('concrete/asset_library');
	$bf = null;
	if ($controller->getFileID() > 0) { 
		$bf = $controller->getFileObject();
	}
?>
<h2><?=t('Flash File')?></h2>
<?=$al->file('ccm-b-file', 'fID', t('Choose File'), $bf);?>

<br/><br/>
<h2><?=t('Quality')?></h2>
<select name="quality">
	<option value="low" <?=($quality == "low"?"selected=\"selected\"":"")?>><?=t('low')?></option>
    <option value="autolow" <?=($quality == "autolow"?"selected=\"selected\"":"")?>><?=t('autolow')?></option>
    <option value="autohigh" <?=($quality == "autohigh"?"selected=\"selected\"":"")?>><?=t('autohigh')?></option>
    <option value="medium" <?=($quality == "medium"?"selected=\"selected\"":"")?>><?=t('medium')?></option>
    <option value="high" <?=($quality == "high"?"selected=\"selected\"":"")?>><?=t('high')?></option>
    <option value="best" <?=($quality == "best"?"selected=\"selected\"":"")?>><?=t('best')?></option>
</select><br /><br />

<h2><?=t('Minimum Flash Player Version')?></h2>
<input type="text" name="minVersion" value="<?=$minVersion?>" /><br /><br />