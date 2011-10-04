<? 
defined('C5_EXECUTE') or die("Access Denied.");
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
?>
<h2><?=t('Flash File')?></h2>
<?=$al->file('ccm-b-file', 'fID', t('Choose File'));?>

<br/>
<h2><?=t('Quality')?></h2>
<select name="quality">
	<option value="low"><?=t('low')?></option>
    <option value="autolow"><?=t('autolow')?></option>
    <option value="autohigh"><?=t('autohigh')?></option>
    <option value="medium"><?=t('medium')?></option>
    <option value="high" selected="selected"><?=t('high')?></option>
    <option value="best"><?=t('best')?></option>
</select><br /><br />

<h2><?=t('Minimum Flash Player Version')?></h2>
<input type="text" name="minVersion" value="8.0" /><br /><br />