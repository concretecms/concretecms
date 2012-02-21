<?
defined('C5_EXECUTE') or die("Access Denied.");
$dt = Loader::helper('form/date_time');
$form = Loader::helper("form");
$tp = new TaskPermission();
$repeats = array(
	'' => t('** Options'), 
	'daily' => t('Every Day'),
	'weekly' => t('Every Week'),
	'monthly' => t('Every Month')
);
$repeatDays = array();
for ($i = 1; $i <= 30; $i++) {
	$repeatDays[$i] = $i;
}
$repeatWeeks = array();
for ($i = 1; $i <= 30; $i++) {
	$repeatWeeks[$i] = $i;
}
$repeatMonths = array();
for ($i = 1; $i <= 12; $i++) {
	$repeatMonths[$i] = $i;
}


Loader::library('3rdparty/Zend/Locale');
$list = Zend_Locale::getTranslationList('Days', ACTIVE_LOCALE);


if (!$tp->canAccessUserSearch() && !$tp->canAccessGroupSearch()) { 
	die(t("Access Denied."));
}

if ($_REQUEST['peID']) {
	$pae = PermissionAccessEntity::getByID($_REQUEST['peID']);
} else {
	$pae = false;
}

if ($_REQUEST['pdID']) {
	$pd = PermissionDuration::getByID($_REQUEST['pdID']);
} else {
	$pd = false;
}

if ($_POST['task'] == 'save_permissions') { 
	$js = Loader::helper('json');
	$r = new stdClass;
	// First, we create a permissions access entity object for this
	
	if (isset($_POST['gID']) || isset($_POST['uID'])) { 
		if (!is_object($pae)) { 
			if (isset($_POST['uID'])) {
				$ui = UserInfo::getByID($_POST['uID']);
				if (is_object($ui)) { 
					$pae = UserPermissionAccessEntity::create($ui);
				}
			} else {
				if (count($_POST['gID']) > 1) { 
					$groups = array();
					foreach($_POST['gID'] as $gID) {
						$g = Group::getByID($gID);
						if (is_object($g)) {
							$groups[] = $g;
						}
					}
					$pae = GroupCombinationPermissionAccessEntity::create($groups);
				} else {
					$g = Group::getByID($_POST['gID'][0]);
					if (is_object($g)) {
						$pae = GroupPermissionAccessEntity::create($g);			
					}
				}
			}
		}
		
		if (is_object($pae)) {

			$dateStart = $dt->translate('pdStartDate');
			$dateEnd = $dt->translate('pdEndDate');
			
			if ($dateStart || $dateEnd) {
				// create a PermissionDuration object
				if ($_REQUEST['pdID']) { 
					$pd = PermissionDuration::getByID($_REQUEST['pdID']);
				} else { 
					$pd = new PermissionDuration();
				}
				
				$pd->setStartDate($dateStart);
				$pd->setEndDate($dateEnd);
				if ($_POST['pdRepeatPeriod']) {
					$pd->setRepeatPeriod($_POST['pdRepeatPeriod']);
					if ($_POST['pdRepeatPeriod'] == 'daily') {
						$pd->setRepeatEveryNum($_POST['pdRepeatPeriodDaysEvery']);
					} else if ($_POST['pdRepeatPeriod'] == 'weekly') {
						$pd->setRepeatEveryNum($_POST['pdRepeatPeriodWeeksEvery']);
						$pd->setRepeatPeriodWeekDays($_POST['pdRepeatPeriodWeeksDays']);
					} else if ($_POST['pdRepeatPeriod'] == 'monthly') {
						$pd->setRepeatMonthBy($_POST['pdRepeatPeriodMonthsRepeatBy']);
						$pd->setRepeatEveryNum($_POST['pdRepeatPeriodMonthsEvery']);					
					}
					$pd->setRepeatPeriodEnd($dt->translate('pdEndRepeatDateSpecific'));
				}
				$pd->save();		
			}
			
		} else {
			$r->error = true;
			$r->message = t('Unable to create permissions access entity object.');
		}

	} else {
		$r->error = true;
		$r->message = t('You must specify at least one user or group.');
	}
	
	if (!$r->error) {
		$r->peID = $pae->getAccessEntityID();
		if (is_object($pd)) {
			$r->pdID = $pd->getPermissionDurationID();
		} else {
			$r->pdID = 0;
		}
	}
	
	print $js->encode($r);
	exit;
}

$members = array();
$pdStartDate = false;
$pdEndDate = false;
$pdRepeats = false;
$pdRepeatPeriod = false;
$pdRepeatPeriodWeekDays = array();
$pdRepeatPeriodDaysEvery = 1;
$pdRepeatPeriodWeeksEvery = 1;
$pdRepeatPeriodMonthsEvery = 1;
$pdRepeatPeriodMonthsRepeatBy = 'month';
$pdEndRepeatDateSpecific = false;
$pdEndRepeatDate = '';
if (is_object($pd)) {
	$pdStartDate = $pd->getStartDate();
	$pdEndDate = $pd->getEndDate();
	$pdRepeats = $pd->repeats();
	$pdRepeatPeriod = $pd->getRepeatPeriod();
	$pdRepeatPeriodWeekDays = $pd->getRepeatPeriodWeekDays();
	$pdRepeatPeriodWeekDays = $pd->getRepeatPeriodWeekDays();
	if ($pdRepeatPeriod == 'daily') {
		$pdRepeatPeriodDaysEvery = $pd->getRepeatPeriodEveryNum();
	}
	if ($pdRepeatPeriod == 'weekly') {
		$pdRepeatPeriodWeeksEvery = $pd->getRepeatPeriodEveryNum();
	}
	if ($pdRepeatPeriod == 'monthly') {
		$pdRepeatPeriodMonthsEvery = $pd->getRepeatPeriodEveryNum();
	}
	if ($pd->getRepeatMonthBy() != '') {
		$pdRepeatPeriodMonthsRepeatBy = $pd->getRepeatMonthBy();
	}
	$pdEndRepeatDateSpecific = $pd->getRepeatPeriodEnd();
	if ($pdEndRepeatDateSpecific) {
		$pdEndRepeatDate = 'date';
	}
}
?>
<div class="ccm-ui" id="ccm-permissions-access-entity-wrapper">

<form id="ccm-permissions-access-entity-form" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/access_entity">
<input type="hidden" name="task" value="save_permissions" />
<?=$form->hidden('accessType');?>
<?=$form->hidden('peID');?>
<?=$form->hidden('pdID');?>

<h4><?=t('Groups or Users')?></h4>

<p><?=t('Who gets access to this permission?')?></p>

<table id="ccm-permissions-access-entity-members">
<tr>
	<th><div style="width: 16px"></div></th>
	<th width="100%"><?=t("Name")?></th>
	<? if (!is_object($pae)) { ?>
		<th><div style="width: 16px"></div></th>
	<? } ?>
</tr>
<tr>
	<td colspan="<? if (!is_object($pae)) { ?>3<? } else { ?>2<? } ?>" id="ccm-permissions-access-entity-members-none"><?=t("No users or groups added.")?></td>
</tr>
</table>
<? if (!is_object($pae)) { ?>
<div style="margin-top: -10px" class="clearfix">
<input type="button" class="btn ccm-button-right small dialog-launch" id="ccm-permissions-access-entity-members-add-user" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_dialog?mode=choose_multiple&cID=<?=$_REQUEST['cID']?>" dialog-modal="false" dialog-width="90%" dialog-title="<?=t('Add User')?>"  dialog-height="70%" value="<?=t('Add User')?>" />
<input type="button" class="btn ccm-button-right small dialog-launch" id="ccm-permissions-access-entity-members-add-group" style="margin-right: 5px" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/select_group?cID=<?=$_REQUEST['cID']?>&include_core_groups=1" dialog-modal="false" dialog-title="<?=t('Add Group')?>" value="<?=t('Add Group')?>" />
</div>
<br/>
<? } ?>

<h4><?=t('Time Settings')?></h4>

<p><?=t('How long will this permission be valid for?')?></p>

<div id="ccm-permissions-access-entity-dates">

<div class="clearfix">
<?=$form->label('pdStartDate_activate', t('From'))?>
<div class="input">
	<?=$dt->datetime('pdStartDate', $pdStartDate, true);?>
</div>
</div>

<div class="clearfix">
<?=$form->label('pdEndDate_activate', t('To'))?>
<div class="input">
	<?=$dt->datetime('pdEndDate', $pdEndDate, true);?>
</div>
</div>

</div>

<div id="ccm-permissions-access-entity-repeat" style="display: none">

<div class="clearfix">
<div class="input">
<ul class="inputs-list">
	<li><label><?=$form->checkbox('pdRepeat', 1, $pdRepeats)?> <span><?=t('Repeat...')?></span></label></li>
</ul>
</div>
</div>

<div id="ccm-permissions-access-entity-repeat-selector" style="display: none">

<div class="clearfix">
<?=$form->label('pdRepeatPeriod', t('Repeats'))?>
<div class="input">
	<?=$form->select('pdRepeatPeriod', $repeats, $pdRepeatPeriod)?>	
</div>
</div>

<div id="ccm-permissions-access-entity-dates-repeat-daily" style="display: none">

<div class="clearfix">
<?=$form->label('pdRepeatPeriodDaysEvery', t('Repeat every'))?>
<div class="input">
	<?=$form->select('pdRepeatPeriodDaysEvery', $repeatDays, $pdRepeatPeriodDaysEvery, array('style' => 'width: 60px'))?>
	<?=t('days')?>
</div>
</div>

</div>

<div id="ccm-permissions-access-entity-dates-repeat-monthly" style="display: none">

<div class="clearfix">
<?=$form->label('pdRepeatPeriodMonthsRepeatBy', t('Repeat By'))?>
<div class="input">
<ul class="inputs-list">
	<li><label><?=$form->radio('pdRepeatPeriodMonthsRepeatBy', 'month', 'month')?> <span><?=t('Day of Month')?></span></label></li>
	<li><label><?=$form->radio('pdRepeatPeriodMonthsRepeatBy', 'week')?> <span><?=t('Day of Week')?></span></label></li>
</ul>
</div>
</div>

<div class="clearfix">
<?=$form->label('pdRepeatPeriodMonthsEvery', t('Repeat every'))?>
<div class="input">
	<?=$form->select('pdRepeatPeriodMonthsEvery', $repeatMonths, $pdRepeatPeriodMonthsEvery, array('style' => 'width: 60px'))?>
	<?=t('months')?>
</div>
</div>

</div>


<div id="ccm-permissions-access-entity-dates-repeat-weekly" style="display: none">

<div id="ccm-permissions-access-entity-dates-repeat-weekly-dow" style="display: none">

<div class="clearfix">
<label><?=t('On')?></label>
<div class="input">
<ul class="inputs-list">
<? foreach($list['format']['wide'] as $key => $value) { ?>
	<li><label><?=$form->checkbox('pdRepeatPeriodWeeksDays[]', $key, in_array($key, $pdRepeatPeriodWeekDays))?> <span><?=$value?></span></label></li>
<? } ?>
</ul>
</div>
</div>

</div>

<div class="clearfix">
<?=$form->label('pdRepeatPeriodWeeksEvery', t('Repeat every'))?>
<div class="input">
	<?=$form->select('pdRepeatPeriodWeeksEvery', $repeatWeeks, $pdRepeatPeriodWeeksEvery, array('style' => 'width: 60px'))?>
	<?=t('weeks')?>
</div>
</div>

</div>

<div id="ccm-permissions-access-entity-dates-repeat-dates" style="display: none">


<div class="clearfix">
<label><?=t('Starts On')?></label>
<div class="input">
	<input type="text" disabled="disabled" value="" name="pdStartRepeatDate"   />
</div>
</div>

<div class="clearfix">
<?=$form->label('pdEndRepeatDate', t('Ends'))?>
<div class="input">
<ul class="inputs-list">
	<li><label><?=$form->radio('pdEndRepeatDate', '', $pdEndRepeatDate)?> <span><?=t('Never')?></span></label></li>
	<li><label><?=$form->radio('pdEndRepeatDate', 'date', $pdEndRepeatDate)?> <?=$dt->date('pdEndRepeatDateSpecific', $pdEndRepeatDateSpecific)?></label></li>
</ul>
</div>
</div>


</div>


</div>
</div>

<div class="dialog-buttons">
	<input type="button" onclick="jQuery.fn.dialog.closeTop()" value="<?=t('Cancel')?>" class="btn" />
	<input type="submit" onclick="$('#ccm-permissions-access-entity-form').submit()" value="<?=t('Save')?>" class="btn primary ccm-button-right" />
</div>
</form>


</div>

<script type="text/javascript">
ccm_accessEntityRemoveRow = function(link) {
	$(link).parent().parent().remove();
	var tbl = $("#ccm-permissions-access-entity-members");
	if (tbl.find('tr').length == 2) { 
		$("#ccm-permissions-access-entity-members-none").show();
		$("#ccm-permissions-access-entity-members-add-user").attr('disabled', false);
		$("#ccm-permissions-access-entity-members-add-group").attr('disabled', false);
	}
}
ccm_triggerSelectGroup = function(gID, gName) {
	if ($("input[class=entitygID][value=" + gID + "]").length == 0) { 
		$("#ccm-permissions-access-entity-members-none").hide();
		var tbl = $("#ccm-permissions-access-entity-members");
		html = '<tr><td><input type="hidden" class="entitygID" name="gID[]" value="' + gID + '" /><img src="<?=ASSETS_URL_IMAGES?>/icons/group.png" /></td><td>' + gName + '</td><? if (!is_object($pae)) { ?><td><a href="javascript:void(0)" onclick="ccm_accessEntityRemoveRow(this)"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" /></a></td><? } ?>';
		tbl.append(html);
		$("#ccm-permissions-access-entity-members-add-user").attr('disabled', true);
	}
}

ccm_triggerSelectUser = function(uID, uName) {
	$("#ccm-permissions-access-entity-members-none").hide();
	var tbl = $("#ccm-permissions-access-entity-members");
	html = '<tr><td><input type="hidden" name="uID" value="' + uID + '" /><img src="<?=ASSETS_URL_IMAGES?>/icons/user.png" /></td><td>' + uName + '</td><? if (!is_object($pae)) { ?><td><a href="javascript:void(0)" onclick="ccm_accessEntityRemoveRow(this)"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" /></a></td><? } ?>';
	tbl.append(html);
	$("#ccm-permissions-access-entity-members-add-group").attr('disabled', true);
	$("#ccm-permissions-access-entity-members-add-user").attr('disabled', true);
}

ccm_accessEntityCalculateRepeatOptions = function() {
	// get the difference between start date and end date
	var sdf = ($("#pdStartDate_dt").datepicker('option', 'dateFormat'));
	var sdfr = $.datepicker.parseDate(sdf, $("#pdStartDate_dt").val());
	var edf = ($("#pdEndDate_dt").datepicker('option', 'dateFormat'));
	var edfr = $.datepicker.parseDate(edf, $("#pdEndDate_dt").val());
	var sh = $("select[name=pdStartDate_h]").val();
	var eh = $("select[name=pdEndDate_h]").val();
	if ($("select[name=pdStartDate_a]").val() == 'PM' && (sh < 12)) { 
		sh = parseInt(sh) + 12;
	} else if (sh == 12 && $("select[name=pdStartDate_a]").val() == 'AM') { 
		sh = 0;
	}
	if ($("select[name=pdEndDate_a]").val() == 'PM' && (eh < 12)) { 
		eh = parseInt(eh) + 12;
	} else if (eh == 12 && $("select[name=pdEndDate_a]").val() == 'AM') { 
		eh = 0;
	}
	var startDate = new Date(sdfr.getFullYear(), sdfr.getMonth(), sdfr.getDate(), sh, $('select[name=pdStartDate_m]').val(), 0);
	var endDate = new Date(edfr.getFullYear(), edfr.getMonth(), edfr.getDate(), eh, $('select[name=pdEndDate_m]').val(), 0);
	var difference = ((endDate.getTime() / 1000) - (startDate.getTime() / 1000));
	if (difference >= 60 * 60 * 24) {
		$('select[name=pdRepeatPeriod] option[value=daily]').attr('disabled', true);
		$("#ccm-permissions-access-entity-dates-repeat-weekly-dow").hide();
	} else {
		$('select[name=pdRepeatPeriod] option[value=daily]').attr('disabled', false);
		$("#ccm-permissions-access-entity-dates-repeat-weekly-dow").show();
	}
	$('input[name=pdStartRepeatDate]').val($("#pdStartDate_dt").val());
	switch(sdfr.getDay()) {
		case 0:
			$("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=sun]").attr('checked', true);
			break;
		case 1:
			$("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=mon]").attr('checked', true);
			break;
		case 2:
			$("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=tue]").attr('checked', true);
			break;
		case 3:
			$("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=wed]").attr('checked', true);
			break;
		case 4:
			$("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=thu]").attr('checked', true);
			break;
		case 5:
			$("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=fri]").attr('checked', true);
			break;
		case 6:
			$("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=sat]").attr('checked', true);
			break;
	}
}

ccm_accessEntityCheckRepeat = function() {
	if ($('input[name=pdRepeat]').is(':checked')) { 
		$("#ccm-permissions-access-entity-repeat-selector").show();
	} else { 
		$("#ccm-permissions-access-entity-repeat-selector").hide();
	}
}

ccm_accessEntityOnActivateDates = function() {
	if ($("#pdStartDate_activate").is(':checked') || $("#pdEndDate_activate").is(':checked')) {
		ccm_accessEntityCalculateRepeatOptions();
	}
	if ($("#pdStartDate_activate").is(':checked') && $("#pdEndDate_activate").is(':checked')) {
		$("#ccm-permissions-access-entity-repeat").show();
	} else {
		$("#ccm-permissions-access-entity-repeat").hide();
	}
}

ccm_accessEntityOnRepeatPeriodChange = function() {
	$("#ccm-permissions-access-entity-dates-repeat-daily").hide();
	$("#ccm-permissions-access-entity-dates-repeat-weekly").hide();
	$("#ccm-permissions-access-entity-dates-repeat-monthly").hide();
	if ($('select[name=pdRepeatPeriod]').val() != '') { 
		$("#ccm-permissions-access-entity-dates-repeat-" + $('select[name=pdRepeatPeriod]').val()).show();
		$("#ccm-permissions-access-entity-dates-repeat-dates").show();
	}
}

ccm_accessEntityCalculateRepeatEnd = function() {
	if ($('input[name=pdEndRepeatDate]:checked').val() == 'date') { 
		$("#ccm-permissions-access-entity-dates-repeat-dates input.ccm-input-date").attr('disabled', false);
	} else {
		$("#ccm-permissions-access-entity-dates-repeat-dates input.ccm-input-date").attr('disabled', true);
	}
}

$(function() {
		
	$("#ccm-permissions-access-entity-form").ajaxForm({
		beforeSubmit: function(r) {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			r = eval('(' + r + ')');
			jQuery.fn.dialog.hideLoader();
			if (r.error) {
				ccmAlert.notice('<?=t("Error")?>', r.message);
			} else { 

				if (typeof(ccm_addAccessEntity) == 'function') { 
					ccm_addAccessEntity(r.peID, r.pdID, '<?=$_REQUEST["accessType"]?>');
				} else {
					alert(r.peID);
					alert(r.pdID);
				}
			}
		}
	});
	
	$("#ccm-permissions-access-entity-dates input[class=ccm-activate-date-time]").click(function() {
		ccm_accessEntityOnActivateDates();
	});	
	
	$("select[name=pdRepeatPeriod]").change(function() {
		ccm_accessEntityOnRepeatPeriodChange();
	});
	
	$("input[name=pdRepeat]").click(function() {
		ccm_accessEntityCheckRepeat();
	});

	$("#ccm-permissions-access-entity-dates span.ccm-input-date-wrapper input, #ccm-permissions-access-entity-dates span.ccm-input-time-wrapper select").change(function() {
		ccm_accessEntityCalculateRepeatOptions();
	});
	$("#ccm-permissions-access-entity-dates-repeat-dates input.ccm-input-date").attr('disabled', true);
	$('input[name=pdEndRepeatDate]').change(function() {
		ccm_accessEntityCalculateRepeatEnd();
	});
	ccm_accessEntityCalculateRepeatOptions();


	<? if (is_object($pae)) { ?>
		<? switch($pae->getAccessEntityType()) {
			case 'C':
				foreach($pae->getGroups() as $g) { ?>
					ccm_triggerSelectGroup(<?=$g->getGroupID()?>, '<?=Loader::helper("text")->entities($g->getGroupName())?>');
				<? }
				break;
			case 'U':
				$ui = $pae->getUserObject(); ?>
				ccm_triggerSelectUser(<?=$ui->getUserID()?>, '<?=$ui->getUserName()?>');
				<?
				break;
			default:
				$g = $pae->getGroupObject(); ?>
				ccm_triggerSelectGroup(<?=$g->getGroupID()?>, '<?=Loader::helper("text")->entities($g->getGroupName())?>');
				<? break;
			} ?>
		
		ccm_accessEntityOnActivateDates();
		ccm_accessEntityCheckRepeat();	
		ccm_accessEntityOnRepeatPeriodChange();
		ccm_accessEntityCalculateRepeatEnd();
		
		<?
		} ?>

});

</script>

<style type="text/css">
#ccm-permissions-access-entity-wrapper .ccm-activate-date-time {margin-right: 8px;}
</style>