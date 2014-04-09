
<? 
defined('C5_EXECUTE') or die("Access Denied.");
$al = Loader::helper('concrete/asset_library');
$bf = null;
if ($controller->getFileID() > 0) { 
	$bf = $controller->getFileObject();
}
?>

<div class="clearfix">
<?=$form->label('ccm-b-file', t('Flash File'))?>
<div class="input">
<?=$al->file('ccm-b-file', 'fID', t('Choose File'), $bf);?>

</div>
</div>

<div class="clearfix">
<?=$form->label('quality', t('Quality'))?>
<div class="input">
<select name="quality" class="span2">
	<option value="low" <?=($quality == "low"?"selected=\"selected\"":"")?>><?=t('low')?></option>
    <option value="autolow" <?=($quality == "autolow"?"selected=\"selected\"":"")?>><?=t('autolow')?></option>
    <option value="autohigh" <?=($quality == "autohigh"?"selected=\"selected\"":"")?>><?=t('autohigh')?></option>
    <option value="medium" <?=($quality == "medium"?"selected=\"selected\"":"")?>><?=t('medium')?></option>
    <option value="high" <?=($quality == "high"?"selected=\"selected\"":"")?>><?=t('high')?></option>
    <option value="best" <?=($quality == "best"?"selected=\"selected\"":"")?>><?=t('best')?></option>
</select>
</div>
</div>

<div class="clearfix">
<?=$form->label('minVersion', t('Minimum Version'))?>
<div class="input">
	<input type="text" name="minVersion" value="<?=$minVersion?>" class="span3"/>
</div>
</div>