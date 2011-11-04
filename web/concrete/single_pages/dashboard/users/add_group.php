<?
defined('C5_EXECUTE') or die("Access Denied.");
$section = 'groups';

$txt = Loader::helper('text');
$ih = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');

$date = Loader::helper('form/date_time');
$form = Loader::helper('form');

?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Group'), false, false, false)?>
<form method="post" id="add-group-form" action="<?=$this->url('/dashboard/users/add_group/', 'do_add')?>">
<div class="ccm-pane-body">
<?=$valt->output('add_or_update_group')?>
<fieldset>
<legend><?=t('Details')?></legend>
<div class="clearfix">
<?=$form->label('gName', t('Name'))?>
<div class="input">
	<input type="text" name="gName" class="span6" value="<?=htmlentities($_POST['gName'])?>" />
</div>
</div>

<div class="clearfix">
<?=$form->label('gDescription', t('Description'))?>
<div class="input">
	<textarea name="gDescription" rows="6" class="span6"><?=$_POST['gDescription']?></textarea>
</div>
</div>
</fieldset>
<fieldset>
<legend><?=t("Group Expiration Options")?></legend>
<label></label>
<div class="input">
<ul class="inputs-list">
	<li>
	<label>
	<?=$form->checkbox('gUserExpirationIsEnabled', 1, false)?>
	<span><?=t('Automatically remove users from this group')?></span></label>
	
	<div style="padding-left: 15px; padding-top: 10px; padding-bottom: 10px">
		<?=$form->select("gUserExpirationMethod", array(
		'SET_TIME' => t('at a specific date and time'),
			'INTERVAL' => t('once a certain amount of time has passed')
		
	), array('disabled' => true));?>	
	</div>	

	</li>
</ul>
</div>

<div id="gUserExpirationSetTimeOptions" style="display: none">
<div class="clearfix">
<?=$form->label('gUserExpirationSetDateTime', t('Expiration Date'))?>
<div class="input">
<?=$date->datetime('gUserExpirationSetDateTime')?>
</div>
</div>
</div>
<div id="gUserExpirationIntervalOptions" style="display: none">
<div class="clearfix">
<label><?=t('Accounts expire after')?></label>
<div class="input">
<table style="width: 1%; margin-bottom: 0px">
<tr>
<td valign="top"><strong><?=t('Days')?></strong><br/>
<?=$form->text('gUserExpirationIntervalDays', array('style' => $style, 'class' => 'span1'))?>
</td>
<td valign="top"><strong><?=t('Hours')?></strong><br/>
<?=$form->text('gUserExpirationIntervalHours', array('style' => $style, 'class' => 'span1'))?>
</td>
<td valign="top"><strong><?=t('Minutes')?></strong><br/>
<?=$form->text('gUserExpirationIntervalMinutes', array('style' => $style, 'class' => 'span1'))?>
</td>
</tr>
</table>
</div>
</div>
</div>

<div id="gUserExpirationAction" style="display: none">
<div class="clearfix">
<?=$form->label('gUserExpirationAction', t('Expiration Action'))?>
<div class="input">
<?=$form->select("gUserExpirationAction", array(
'REMOVE' => t('Remove the user from this group'),
	'DEACTIVATE' => t('Deactivate the user account'),
	'REMOVE_DEACTIVATE' => t('Remove the user from the group and deactivate the account')

));?>	
</div>
</div>
</div>
</fieldset>
</div>
<div class="ccm-pane-footer">
<input type="hidden" name="add" value="1" /><input type="submit" name="submit" value="<?=t('Add Group')?>" class="btn primary" />
</div>

</form>	
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>

<script type="text/javascript">
ccm_checkGroupExpirationOptions = function() {
	var sel = $("select[name=gUserExpirationMethod]");
	var cb = $("input[name=gUserExpirationIsEnabled]");
	if (cb.prop('checked')) {
		sel.attr('disabled', false);
		switch(sel.val()) {
			case 'SET_TIME':
				$("#gUserExpirationSetTimeOptions").show();
				$("#gUserExpirationIntervalOptions").hide();
				break;
			case 'INTERVAL': 
				$("#gUserExpirationSetTimeOptions").hide();
				$("#gUserExpirationIntervalOptions").show();
				break;				
		}
		$("#gUserExpirationAction").show();
	} else {
		sel.attr('disabled', true);	
		$("#gUserExpirationSetTimeOptions").hide();
		$("#gUserExpirationIntervalOptions").hide();
		$("#gUserExpirationAction").hide();
	}
}

$(function() {
	$("input[name=gUserExpirationIsEnabled]").click(ccm_checkGroupExpirationOptions);
	$("select[name=gUserExpirationMethod]").change(ccm_checkGroupExpirationOptions);
	ccm_checkGroupExpirationOptions();
});
</script>