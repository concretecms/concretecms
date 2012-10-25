<?
defined('C5_EXECUTE') or die("Access Denied.");
$section = 'groups';

function checkExpirationOptions($g) {
	if ($_POST['gUserExpirationIsEnabled']) {
		$date = Loader::helper('form/date_time');
		switch($_POST['gUserExpirationMethod']) {
			case 'SET_TIME':
				$g->setGroupExpirationByDateTime($date->translate('gUserExpirationSetDateTime'), $_POST['gUserExpirationAction']);
				break;
			case 'INTERVAL':
				$g->setGroupExpirationByInterval($_POST['gUserExpirationIntervalDays'], $_POST['gUserExpirationIntervalHours'], $_POST['gUserExpirationIntervalMinutes'], $_POST['gUserExpirationAction']);
				break;
		}
	} else {
		$g->removeGroupExpiration();
	}
}

if ($_REQUEST['task'] == 'edit') {
	$g = Group::getByID(intval($_REQUEST['gID']));
	if (is_object($g)) { 		
		if ($_POST['update']) {
		
			$gName = $_POST['gName'];
			$gDescription = $_POST['gDescription'];
			
		} else {
			
			$gName = $g->getGroupName();
			$gDescription = $g->getGroupDescription();
		
		}
		
		$editMode = true;
	}
}

$txt = Loader::helper('text');
$ih = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');

if (!$editMode) {

Loader::model('search/group');
$gl = new GroupSearch();
if (isset($_GET['gKeywords'])) {
	$gl->filterByKeywords($_GET['gKeywords']);
}

$gResults = $gl->getPage();

?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Groups'), false, 'span10 offset1', false)?>
<?
$tp = new TaskPermission();
if ($tp->canAccessGroupSearch()) { ?>

<div class="ccm-pane-options">
<form method="get" class="form-horizontal" action="<?=$this->url('/dashboard/users/groups')?>">
<div class="ccm-pane-options-permanent-search">
<div class="span8">
<? $form = Loader::helper('form'); ?>
<?=$form->label('gKeywords', t('Keywords'))?>
<div class="controls">
	<input type="text" name="gKeywords" value="<?=htmlentities($_REQUEST['gKeywords'])?>"  />
	<input class="btn" type="submit" value="<?=t('Search')?>" />
</div>
<input type="hidden" name="group_submit_search" value="1" />
</div>
</div>
</form>
</div>
<div class="ccm-pane-body <? if (!$gl->requiresPaging()) { ?> ccm-pane-body-footer <? } ?>">

	<a href="<?php echo View::url('/dashboard/users/add_group')?>" style="float: right; position:relative;top:-5px"  class="btn primary"><?php echo t("Add Group")?></a>

<? if (count($gResults) > 0) { 
	$gl->displaySummary();
$gp = new Permissions();
$canEditGroups = $gp->canEditGroups();
?>

	<style type="text/css">
	div.ccm-paging-top {padding-bottom:10px;}
	</style>

<?
	
foreach ($gResults as $g) { ?>
	
	<div class="ccm-group">
		<<? if ($canEditGroups) { ?>a<? } else {?>span<? } ?> class="ccm-group-inner" <? if ($canEditGroups) { ?>href="<?=$this->url('/dashboard/users/groups?task=edit&gID=' . $g['gID'])?>"<? } ?> style="background-image: url(<?=ASSETS_URL_IMAGES?>/icons/group.png)"><?=t($g['gName'])?><? if ($canEditGroups) { ?></a><? } else {?></span><? } ?>
		<? if ($g['gDescription']) { ?>
			<div class="ccm-group-description"><?=$g['gDescription']?></div>
		<? } ?>
	</div>


<? }

} else { ?>

	<p><?=t('No groups found.')?></p>
	
<? } ?>
</div>
<? if ($gl->requiresPaging()) { ?>
<div class="ccm-pane-footer">
	<?=$gl->displayPagingV2();?>
</div>
<? } ?>

<? } else { ?>
<div class="ccm-pane-body ccm-pane-body-footer">
	<p><?=t('You do not have access to group search. This setting may be changed in the access section of the dashboard settings page.')?></p>
</div>
<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>

<? } else { ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Group'), false, false, false)?>
<form method="post"  class="form-horizontal" id="update-group-form" action="<?=$this->url('/dashboard/users/groups/', 'update_group')?>">
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
				location.href = "<?=$this->url('/dashboard/users/groups', 'delete', intval($_REQUEST['gID']), $valt->generate('delete_group_' . intval($_REQUEST['gID']) ))?>";				
			}
		}
		</script>

	<? } ?>

	<fieldset>
	<div class="control-group">
	<?=$form->label('gName', t('Name'))?>
	<div class="controls">
		<input type="text" name="gName" class="span6" value="<?=Loader::helper('text')->entities(t($gName))?>" />
	</div>
	</div>
	
	<div class="control-group">
	<?=$form->label('gDescription', t('Description'))?>
	<div class="controls">
		<textarea name="gDescription" rows="6" class="span6"><?=Loader::helper("text")->entities($gDescription)?></textarea>
	</div>
	</div>
	</fieldset>
	<fieldset>
	<legend><?=t("Group Expiration Options")?></legend>
	<div class="control-group">
	<div class="controls">

		<label class="checkbox">
		<?=$form->checkbox('gUserExpirationIsEnabled', 1, $g->isGroupExpirationEnabled())?>
		<span><?=t('Automatically remove users from this group')?></span></label>
		
	</div>
	
	<div class="controls" style="padding-left: 18px">
		<?=$form->select("gUserExpirationMethod", array(
			'SET_TIME' => t('at a specific date and time'),
				'INTERVAL' => t('once a certain amount of time has passed')
			
		), $g->getGroupExpirationMethod(), array('disabled' => true));?>	
	</div>	
	</div>
	
	
	<div id="gUserExpirationSetTimeOptions" style="display: none">
	<div class="control-group">
	<?=$form->label('gUserExpirationSetDateTime', t('Expiration Date'))?>
	<div class="controls">
	<?=$date->datetime('gUserExpirationSetDateTime', $g->getGroupExpirationDateTime())?>
	</div>
	</div>
	</div>
	<div id="gUserExpirationIntervalOptions" style="display: none">
	<div class="control-group">
	<label><?=t('Accounts expire after')?></label>
	<div class="controls">
	<table class="table table-condensed" style="width: auto">
	<tr>
	<?
	$days = $g->getGroupExpirationIntervalDays();
	$hours = $g->getGroupExpirationIntervalHours();
	$minutes = $g->getGroupExpirationIntervalMinutes();
	$style = 'width: 60px';
	?>
	<td valign="top"><strong><?=t('Days')?></strong><br/>
	<?=$form->text('gUserExpirationIntervalDays', $days, array('style' => $style, 'class' => 'span1'))?>
	</td>
	<td valign="top"><strong><?=t('Hours')?></strong><br/>
	<?=$form->text('gUserExpirationIntervalHours', $hours, array('style' => $style, 'class' => 'span1'))?>
	</td>
	<td valign="top"><strong><?=t('Minutes')?></strong><br/>
	<?=$form->text('gUserExpirationIntervalMinutes', $minutes, array('style' => $style, 'class' => 'span1'))?>
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
	), $g->getGroupExpirationAction());?>	
	</div>
	</div>
	</div>
	<input type="hidden" name="gID" value="<?=intval($_REQUEST['gID'])?>" />
	<input type="hidden" name="task" value="edit" />
	</fieldset>
</div>
<div class="ccm-pane-footer">
	<?=$ih->submit(t('Update'), 'update-group-form', 'right', 'primary')?>
	<? print $ih->button_js(t('Delete'), "deleteGroup()", 'right', 'error');?>
	<?=$ih->button(t('Cancel'), $this->url('/dashboard/users/groups'), 'left')?>
</div>
</form>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
<? } ?>

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
	/*
	$("div#gUserExpirationIntervalOptions input").focus(function() {
		if ($('input[name=gUserExpirationIntervalDays]').val() == '<?=t("Days")?>' &&
			$('input[name=gUserExpirationIntervalHours]').val() == '<?=t("Hours")?>' &&
			$('input[name=gUserExpirationIntervalMinutes]').val() == '<?=t("Minutes")?>') {
			$("div#gUserExpirationIntervalOptions input").val("");
			$("div#gUserExpirationIntervalOptions input").css('color', '#000');
		}
	});
	$("div#gUserExpirationIntervalOptions input").blur(function() {
		if ($('input[name=gUserExpirationIntervalDays]').val() == '' &&
			$('input[name=gUserExpirationIntervalHours]').val() == '' &&
			$('input[name=gUserExpirationIntervalMinutes]').val() == '') {
			$('input[name=gUserExpirationIntervalDays]').val('<?=t("Days")?>');
			$('input[name=gUserExpirationIntervalHours]').val('<?=t("Hours")?>');
			$('input[name=gUserExpirationIntervalMinutes]').val('<?=t("Minutes")?>');
			$("div#gUserExpirationIntervalOptions input").css('color', '#aaa');
		}
	});
	*/
});
</script>

