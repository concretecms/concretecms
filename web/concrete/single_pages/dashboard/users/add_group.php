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
<form class="form-horizontal" method="post" id="add-group-form" action="<?=$this->url('/dashboard/users/add_group/', 'do_add')?>">
<div class="ccm-pane-body">
<?=$valt->output('add_or_update_group')?>
<fieldset>
	<legend><?=t('Details')?></legend>

<div class="control-group">
<?=$form->label('gName', t('Name'))?>
<div class="controls">
	<input type="text" name="gName" class="span6" value="<?=htmlentities($_POST['gName'])?>" />
</div>
</div>

<div class="control-group">
<?=$form->label('gDescription', t('Description'))?>
<div class="controls">
	<?=$form->textarea('gDescription', array('rows' => 6, 'class' =>'span6'))?>
</div>
</div>
</fieldset>
<? if (ENABLE_USER_PROFILES) { ?>
<fieldset>
	<legend><?=t('Badge')?></legend>
	<div class="control-group">
	<div class="controls">
	<label class="checkbox">
	<?=$form->checkbox('gIsBadge', 1, false)?>
	<span><?=t('This group is a badge.')?> <i class="icon-question-sign" title="<?=t('Badges are publicly viewable in user profiles, and display pictures and a custom description. Badges can be automatically assigned or given out by administrators.')?>"></i> </span>
	</label>
	</div>
	</div>
	
<div id="gUserBadgeOptions" style="display: none">
	<div class="control-group">
		<label class="control-label"><?=t('Image')?></label>
		<div class="controls">
			<?

			$af = Loader::helper('concrete/asset_library');
			print $af->image('gBadgeFID', 'gBadgeFID', t('Choose Badge Image'), $badgeImage);
			?>

		</div>
	</div>

	<div class="control-group">
	<?=$form->label('gBadgeDescription', t('Badge Description'))?>
	<div class="controls">
		<?=$form->textarea('gBadgeDescription', array('rows' => 6, 'class' =>'span6'))?>
	</div>
	</div>

	<div class="control-group">
	<?=$form->label('gBadgeCommunityPointValue', t('Community Points'))?>
	<div class="controls">
		<?=$form->text('gBadgeCommunityPointValue', GROUP_BADGE_DEFAULT_POINT_VALUE, array('class' => 'span1'))?>
	</div>
	</div>


</div>

</fieldset>
<? } ?>

<fieldset>
	<legend><?=t('Automation')?></legend>
	<div class="control-group">
	<div class="controls">
	<label class="checkbox">
	<?=$form->checkbox('gIsAutomated', 1, false)?>
	<span><?=t('This group is automatically entered.')?> <i class="icon-question-sign" title="<?=t("Automated Groups aren't assigned by administrators. They are checked against code at certain times that determines whether users should enter them.")?>"></i> </span>
	</label>
	</div>
	</div>
	
<div id="gAutomationOptions" style="display: none">
	<div class="control-group">
	<label class="control-label"><?=t('Check Group')?></label>
	<div class="controls">
		<label class="checkbox">
			<?=$form->checkbox('gCheckAutomationOnRegister', 1)?>
			<span><?=t('When a user registers.')?></span>
		</label>
		<label class="checkbox">
			<?=$form->checkbox('gCheckAutomationOnLogin', 1)?>
			<span><?=t('When a user signs in.')?></span>
		</label>
		<label class="checkbox">
			<?=$form->checkbox('gCheckAutomationOnJobRun', 1)?>
			<span><?=t('When the "Check Automated Groups" Job runs.')?></span>
		</label>
	</div>
	</div>

</div>


<div class="control-group">
	<div class="controls">
	<label class="checkbox">
	<?=$form->checkbox('gUserExpirationIsEnabled', 1, false)?>
	<span><?=t('Automatically remove users from this group.')?></span></label>
	</div>
	
	<div class="controls" style="">
	<?=$form->select("gUserExpirationMethod", array(
		'SET_TIME' => t('at a specific date and time'),
			'INTERVAL' => t('once a certain amount of time has passed')
		
	), array('disabled' => true));?>	
	</div>	
</div>

<div id="gUserExpirationSetTimeOptions" style="display: none">
<div class="control-group">
<?=$form->label('gUserExpirationSetDateTime', t('Expiration Date'))?>
<div class="controls">
<?=$date->datetime('gUserExpirationSetDateTime')?>
</div>
</div>
</div>
<div id="gUserExpirationIntervalOptions" style="display: none">
<div class="control-group">
<label class="control-label"><?=t('Accounts expire after')?></label>
<div class="controls">
<table class="table " style="width: auto">
<tr>
	<th><?=t('Days')?></th>
	<th><?=t('Hours')?></th>
	<th><?=t('Minutes')?></th>
</tr>
<tr>	
<td>
<?=$form->text('gUserExpirationIntervalDays', array('style' => $style, 'class' => 'span1'))?>
</td>
<td>
<?=$form->text('gUserExpirationIntervalHours', array('style' => $style, 'class' => 'span1'))?>
</td>
<td>
<?=$form->text('gUserExpirationIntervalMinutes', array('style' => $style, 'class' => 'span1'))?>
</td>
</tr>
</table>
</div>
</div>
</div>

<div id="gUserExpirationAction" style="display: none">
<div class="control-group">
<?=$form->label('gUserExpirationAction', t('Expiration Action'))?>
<div class="controls">
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
<input type="hidden" name="add" value="1" /><input type="submit" name="submit" value="<?=t('Add')?>" class="btn ccm-button-right primary" />
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
	$('input[name=gIsBadge]').on('click', function() {
		if ($(this).is(':checked')) {
			$('#gUserBadgeOptions').show();
		} else {
			$('#gUserBadgeOptions').hide();
		}
	}).triggerHandler('click');
	$('input[name=gIsAutomated]').on('click', function() {
		if ($(this).is(':checked')) {
			$('#gAutomationOptions').show();
		} else {
			$('#gAutomationOptions').hide();
		}
	}).triggerHandler('click');
	$('.icon-question-sign').tooltip();
	ccm_checkGroupExpirationOptions();
});
</script>