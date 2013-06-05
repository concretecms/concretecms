<? 
defined('C5_EXECUTE') or die("Access Denied.");
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
?>

<div class="clearfix">
<?=$form->label('ccm-b-file', t('Flash File'))?>
<div class="input">
<?=$al->file('ccm-b-file', 'fID', t('Choose File'));?>

</div>
</div>

<div class="clearfix">
<?=$form->label('quality', t('Quality'))?>
<div class="input">
<select name="quality" class="span2">
	<option value="low"><?=t('low')?></option>
    <option value="autolow"><?=t('autolow')?></option>
    <option value="autohigh"><?=t('autohigh')?></option>
    <option value="medium"><?=t('medium')?></option>
    <option value="high" selected="selected"><?=t('high')?></option>
    <option value="best"><?=t('best')?></option>
</select>
</div>
</div>

<div class="clearfix">
<?=$form->label('minVersion', t('Minimum Version'))?>
<div class="input">
	<input type="text" name="minVersion" value="8.0" class="span3"/>
</div>
</div>