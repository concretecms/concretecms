<?
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$ih = Loader::helper('concrete/ui');
if (isset($group)) { 

	if ($_POST['update']) {
		$gName = $_POST['gName'];
		$gDescription = $_POST['gDescription'];
	} else {
		$gName = $group->getGroupName();
		$gDescription = $group->getGroupDescription();
	}

	?>

<form method="post"  class="form-horizontal" id="update-group-form" action="<?=$view->url('/dashboard/users/groups/', 'update_group')?>">
<?=$valt->output('add_or_update_group')?>
<div class="ccm-pane-body">
	<?
	$form = Loader::helper('form');
	$date = Loader::helper('form/date_time');
	$u=new User();

	$delConfirmJS = t('Are you sure you want to permanently remove this group?');
	if($u->isSuperUser() == false){ ?>
		<?=t('You must be logged in as %s to remove groups.', USER_SUPER)?>			
	<? }else{ ?>   

		<script type="text/javascript">
		deleteGroup = function() {
			if (confirm('<?=$delConfirmJS?>')) { 
				location.href = "<?=$view->url('/dashboard/users/groups', 'delete', $group->getGroupID(), $valt->generate('delete_group_' . $group->getGroupID() ))?>";				
			}
		}
		</script>

	<? } ?>

	<fieldset>
		<legend><?=t('Group Details')?></legend>
	<div class="control-group">
	<?=$form->label('gName', t('Name'))?>
	<div class="controls">
		<input type="text" name="gName" class="span6" value="<?=Loader::helper('text')->entities($gName)?>" />
	</div>
	</div>
	
	<div class="control-group">
	<?=$form->label('gDescription', t('Description'))?>
	<div class="controls">
		<textarea name="gDescription" rows="6" class="span6"><?=Loader::helper("text")->entities($gDescription)?></textarea>
	</div>
	</div>
	</fieldset>

	<? if (ENABLE_USER_PROFILES) { ?>
	<fieldset>
		<legend><?=t('Badge Details')?></legend>
		<div class="control-group">
		<div class="controls">
		<label class="checkbox">
		<?=$form->checkbox('gIsBadge', 1, $group->isGroupBadge())?>
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
				print $af->image('gBadgeFID', 'gBadgeFID', t('Choose Badge Image'), $group->getGroupBadgeImageObject());
				?>

			</div>
		</div>

		<div class="control-group">
		<?=$form->label('gBadgeDescription', t('Badge Description'))?>
		<div class="controls">
			<?=$form->textarea('gBadgeDescription', $group->getGroupBadgeDescription(), array('rows' => 6, 'class' =>'span6'))?>
		</div>
		</div>

		<div class="control-group">
		<?=$form->label('gBadgeCommunityPointValue', t('Community Points'))?>
		<div class="controls">
			<?=$form->text('gBadgeCommunityPointValue', $group->getGroupBadgeCommunityPointValue(), array('class' => 'span1'))?>
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
		<?=$form->checkbox('gIsAutomated', 1, $group->isGroupAutomated())?>
		<span><?=t('This group is automatically entered.')?> <i class="icon-question-sign" title="<?=t("Automated Groups aren't assigned by administrators. They are checked against code at certain times that determines whether users should enter them.")?>"></i> </span>
		</label>
		</div>
		</div>
		
	<div id="gAutomationOptions" style="display: none">
		<div class="control-group">
		<label class="control-label"><?=t('Check Group')?></label>
		<div class="controls">
			<label class="checkbox">
				<?=$form->checkbox('gCheckAutomationOnRegister', 1, $group->checkGroupAutomationOnRegister())?>
				<span><?=t('When a user registers.')?></span>
			</label>
			<label class="checkbox">
				<?=$form->checkbox('gCheckAutomationOnLogin', 1, $group->checkGroupAutomationOnLogin())?>
				<span><?=t('When a user signs in.')?></span>
			</label>
			<label class="checkbox">
				<?=$form->checkbox('gCheckAutomationOnJobRun', 1, $group->checkGroupAutomationOnJobRun())?>
				<span><?=t('When the "Check Automated Groups" Job runs.')?></span>
			</label>
		</div>
		</div>

		<div class="alert alert-info">
			<?
			$path = $group->getGroupAutomationControllerFile();
			print t('Make sure a code file exists at %s', str_replace(array(DIR_APPLICATION, DIR_BASE_CORE), '', $path));
			?>
		</div>
	</div>

	<div class="control-group">
	<div class="controls">

		<label class="checkbox">
		<?=$form->checkbox('gUserExpirationIsEnabled', 1, $group->isGroupExpirationEnabled())?>
		<span><?=t('Automatically remove users from this group')?></span></label>
		
	</div>
	
	<div class="controls" style="">
		<?=$form->select("gUserExpirationMethod", array(
			'SET_TIME' => t('at a specific date and time'),
				'INTERVAL' => t('once a certain amount of time has passed')
			
		), $group->getGroupExpirationMethod(), array('disabled' => true));?>	
	</div>	
	</div>
	
	
	<div id="gUserExpirationSetTimeOptions" style="display: none">
	<div class="control-group">
	<?=$form->label('gUserExpirationSetDateTime', t('Expiration Date'))?>
	<div class="controls">
	<?=$date->datetime('gUserExpirationSetDateTime', $group->getGroupExpirationDateTime())?>
	</div>
	</div>
	</div>
	<div id="gUserExpirationIntervalOptions" style="display: none">
	<div class="control-group">
	<label class="control-label"><?=t('Accounts expire after')?></label>
	<div class="controls">
	<table class="table" style="width: auto">
	<tr>
		<th><?=t('Days')?></th>
		<th><?=t('Hours')?></th>
		<th><?=t('Minutes')?></th>
	</tr>

	<tr>

	<?
	$days = $group->getGroupExpirationIntervalDays();
	$hours = $group->getGroupExpirationIntervalHours();
	$minutes = $group->getGroupExpirationIntervalMinutes();
	$style = 'width: 60px';
	?>
	<td valign="top">
	<?=$form->text('gUserExpirationIntervalDays', $days, array('style' => $style, 'class' => 'span1'))?>
	</td>
	<td valign="top">
	<?=$form->text('gUserExpirationIntervalHours', $hours, array('style' => $style, 'class' => 'span1'))?>
	</td>
	<td valign="top">
	<?=$form->text('gUserExpirationIntervalMinutes', $minutes, array('style' => $style, 'class' => 'span1'))?>
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
	), $group->getGroupExpirationAction());?>	
	</div>
	</div>
	</div>
	<input type="hidden" name="gID" value="<?=$group->getGroupID()?>" />
	</fieldset>
</div>
<div class="ccm-pane-footer">
	<button class="btn pull-right btn-primary" style="margin-left: 10px" type="submit"><?=t('Update Group')?></button>
	<a href="<?=$view->url('/dashboard/users/groups')?>" class="btn btn-default pull-left"><?=t('Cancel')?></a>
	<? if ($u->isSuperUser()) { ?>
		<? print $ih->button_js(t('Delete'), "deleteGroup()", 'right', 'error');?>
		<? } ?>
</div>
</form>

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
	
});
</script>
<? } else { ?>

	<? if ($canAddGroup) { ?>
	<div class="ccm-dashboard-header-buttons">
		<a href="<?php echo View::url('/dashboard/users/add_group')?>" class="btn btn-primary"><?php echo t("Add Group")?></a>
	</div>
	<? } ?>


<? Loader::element('group/search', array('controller' => $searchController, 'selectMode' => false))?>


<? } ?>