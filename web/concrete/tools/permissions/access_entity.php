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

?>
<div class="ccm-ui" id="ccm-permissions-access-entity-wrapper">

<form>

<h4><?=t('Groups or Users')?></h4>

<p><?=t('Who gets access to this permission?')?></p>

<table id="ccm-permissions-access-entity-members">
<tr>
	<th><div style="width: 16px"></div></th>
	<th width="100%"><?=t("Name")?></th>
	<th><div style="width: 16px"></div></th>
</tr>
<tr>
	<td colspan="3" id="ccm-permissions-access-entity-members-none"><?=t("No users or groups added.")?></td>
</tr>
</table>
<div style="margin-top: -10px" class="clearfix">
<input type="button" class="btn ccm-button-right small dialog-launch" id="ccm-permissions-access-entity-members-add-user" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_dialog?mode=choose_multiple&cID=<?=$_REQUEST['cID']?>" dialog-modal="false" dialog-width="90%" dialog-title="<?=t('Add User')?>"  dialog-height="70%" value="<?=t('Add User')?>" />
<input type="button" class="btn ccm-button-right small dialog-launch" id="ccm-permissions-access-entity-members-add-group" style="margin-right: 5px" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/select_group?cID=<?=$_REQUEST['cID']?>" dialog-modal="false" dialog-title="<?=t('Add Group')?>" value="<?=t('Add Group')?>" />
</div>
<br/>

<h4><?=t('Time Settings')?></h4>

<p><?=t('How long will this permission be valid for?')?></p>

<div id="ccm-permissions-access-entity-dates">

<div class="clearfix">
<?=$form->label('peStartDate_activate', t('From'))?>
<div class="input">
	<?=$dt->datetime('peStartDate', '', true);?>
</div>
</div>

<div class="clearfix">
<?=$form->label('peEndDate_activate', t('To'))?>
<div class="input">
	<?=$dt->datetime('peEndDate', '', true);?>
</div>
</div>

</div>

<div id="ccm-permissions-access-entity-repeat" style="display: none">

<div class="clearfix">
<div class="input">
<ul class="inputs-list">
	<li><label><?=$form->checkbox('peRepeat', 1)?> <span><?=t('Repeat...')?></span></label></li>
</ul>
</div>
</div>

<div id="ccm-permissions-access-entity-repeat-selector" style="display: none">

<div class="clearfix">
<?=$form->label('peRepeatPeriod', t('Repeats'))?>
<div class="input">
	<?=$form->select('peRepeatPeriod', $repeats, '')?>	
</div>
</div>

<div id="ccm-permissions-access-entity-dates-repeat-daily" style="display: none">

<div class="clearfix">
<?=$form->label('peRepeatPeriodDaysEvery', t('Repeat every'))?>
<div class="input">
	<?=$form->select('peRepeatPeriodDaysEvery', $repeatDays, 1, array('style' => 'width: 60px'))?>
	<?=t('days')?>
</div>
</div>

</div>

<div id="ccm-permissions-access-entity-dates-repeat-monthly" style="display: none">

<div class="clearfix">
<?=$form->label('peRepeatPeriodMonthsRepeatBy', t('Repeat By'))?>
<div class="input">
<ul class="inputs-list">
	<li><label><?=$form->radio('peRepeatPeriodMonthsRepeatBy', 'month', 'month')?> <span><?=t('Day of Month')?></span></label></li>
	<li><label><?=$form->radio('peRepeatPeriodMonthsRepeatBy', 'week')?> <span><?=t('Day of Week')?></span></label></li>
</ul>
</div>
</div>

<div class="clearfix">
<?=$form->label('peRepeatPeriodMonthsEvery', t('Repeat every'))?>
<div class="input">
	<?=$form->select('peRepeatPeriodMonthsEvery', $repeatMonths, 1, array('style' => 'width: 60px'))?>
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
	<li><label><?=$form->checkbox('peRepeatPeriodWeeksDays[]', $key)?> <span><?=$value?></span></label></li>
<? } ?>
</ul>
</div>
</div>

</div>

<div class="clearfix">
<?=$form->label('peRepeatPeriodWeeksEvery', t('Repeat every'))?>
<div class="input">
	<?=$form->select('peRepeatPeriodWeeksEvery', $repeatWeeks, 1, array('style' => 'width: 60px'))?>
	<?=t('weeks')?>
</div>
</div>

</div>

<div id="ccm-permissions-access-entity-dates-repeat-dates" style="display: none">


<div class="clearfix">
<label><?=t('Starts On')?></label>
<div class="input">
	<input type="text" disabled="disabled" value="" name="peStartRepeatDate"   />
</div>
</div>

<div class="clearfix">
<?=$form->label('peEndRepeatDate', t('Ends'))?>
<div class="input">
<ul class="inputs-list">
	<li><label><?=$form->radio('peEndRepeatDate', '')?> <span><?=t('Never')?></span></label></li>
	<li><label><?=$form->radio('peEndRepeatDate', 'date')?> <?=$dt->date('peEndRepeatDateSpecific')?></label></li>
</ul>
</div>
</div>


</div>


</div>
</div>

<div class="dialog-buttons">
	<input type="button" onclick="jQuery.fn.dialog.closeTop()" value="<?=t('Cancel')?>" class="btn" />
	<input type="submit" value="<?=t('Save')?>" class="btn primary ccm-button-right" />
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
		html = '<tr><td><input type="hidden" class="entitygID" name="gID[]" value="' + gID + '" /><img src="<?=ASSETS_URL_IMAGES?>/icons/group.png" /></td><td>' + gName + '</td><td><a href="javascript:void(0)" onclick="ccm_accessEntityRemoveRow(this)"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" /></a></td>';
		tbl.append(html);
		$("#ccm-permissions-access-entity-members-add-user").attr('disabled', true);
	}
}

ccm_triggerSelectUser = function(uID, uName) {
	$("#ccm-permissions-access-entity-members-none").hide();
	var tbl = $("#ccm-permissions-access-entity-members");
	html = '<tr><td><input type="hidden" name="uID[]" value="' + uID + '" /><img src="<?=ASSETS_URL_IMAGES?>/icons/user.png" /></td><td>' + uName + '</td><td><a href="javascript:void(0)" onclick="ccm_accessEntityRemoveRow(this)"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" /></a></td>';
	tbl.append(html);
	$("#ccm-permissions-access-entity-members-add-group").attr('disabled', true);
	$("#ccm-permissions-access-entity-members-add-user").attr('disabled', true);
}

ccm_accessEntityCalculateRepeatOptions = function() {
	// get the difference between start date and end date
	var sdf = ($("#peStartDate_dt").datepicker('option', 'dateFormat'));
	var sdfr = $.datepicker.parseDate(sdf, $("#peStartDate_dt").val());
	var edf = ($("#peEndDate_dt").datepicker('option', 'dateFormat'));
	var edfr = $.datepicker.parseDate(edf, $("#peEndDate_dt").val());
	var sh = $("select[name=peStartDate_h]").val();
	var eh = $("select[name=peEndDate_h]").val();
	if ($("select[name=peStartDate_a]").val() == 'PM' && (sh < 12)) { 
		sh = parseInt(sh) + 12;
	} else if (sh == 12 && $("select[name=peStartDate_a]").val() == 'AM') { 
		sh = 0;
	}
	if ($("select[name=peEndDate_a]").val() == 'PM' && (eh < 12)) { 
		eh = parseInt(eh) + 12;
	} else if (eh == 12 && $("select[name=peEndDate_a]").val() == 'AM') { 
		eh = 0;
	}
	var startDate = new Date(sdfr.getFullYear(), sdfr.getMonth(), sdfr.getDate(), sh, $('select[name=peStartDate_m]').val(), 0);
	var endDate = new Date(edfr.getFullYear(), edfr.getMonth(), edfr.getDate(), eh, $('select[name=peEndDate_m]').val(), 0);
	var difference = ((endDate.getTime() / 1000) - (startDate.getTime() / 1000));
	if (difference >= 60 * 60 * 24) {
		$('select[name=peRepeatPeriod] option[value=daily]').attr('disabled', true);
		$("#ccm-permissions-access-entity-dates-repeat-weekly-dow").hide();
	} else {
		$('select[name=peRepeatPeriod] option[value=daily]').attr('disabled', false);
		$("#ccm-permissions-access-entity-dates-repeat-weekly-dow").show();
	}
	$('input[name=peStartRepeatDate]').val($("#peStartDate_dt").val());
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

$(function() {
	$("#ccm-permissions-access-entity-dates input[class=ccm-activate-date-time]").click(function() {
		if ($("#peStartDate_activate").is(':checked') || $("#peEndDate_activate").is(':checked')) {
			ccm_accessEntityCalculateRepeatOptions();
		}
		if ($("#peStartDate_activate").is(':checked') && $("#peEndDate_activate").is(':checked')) {
			$("#ccm-permissions-access-entity-repeat").show();
		} else {
			$("#ccm-permissions-access-entity-repeat").hide();
		}
	});	
	
	$("select[name=peRepeatPeriod]").change(function() {
		$("#ccm-permissions-access-entity-dates-repeat-daily").hide();
		$("#ccm-permissions-access-entity-dates-repeat-weekly").hide();
		$("#ccm-permissions-access-entity-dates-repeat-monthly").hide();
		if ($(this).val() != '') { 
			$("#ccm-permissions-access-entity-dates-repeat-" + $(this).val()).show();
			$("#ccm-permissions-access-entity-dates-repeat-dates").show();
		}
	});
	
	$("input[name=peRepeat]").click(function() {
		if ($(this).is(':checked')) { 
			$("#ccm-permissions-access-entity-repeat-selector").show();
		} else { 
			$("#ccm-permissions-access-entity-repeat-selector").hide();
		}
	});

	$("#ccm-permissions-access-entity-dates span.ccm-input-date-wrapper input, #ccm-permissions-access-entity-dates span.ccm-input-time-wrapper select").change(function() {
		ccm_accessEntityCalculateRepeatOptions();
	});
	$("#ccm-permissions-access-entity-dates-repeat-dates input.ccm-input-date").attr('disabled', true);
	$('input[name=peEndRepeatDate]').change(function() {
		if ($(this).val() == 'date') { 
			$("#ccm-permissions-access-entity-dates-repeat-dates input.ccm-input-date").attr('disabled', false);
		} else {
			$("#ccm-permissions-access-entity-dates-repeat-dates input.ccm-input-date").attr('disabled', true);
		}
	});
	ccm_accessEntityCalculateRepeatOptions();
});

</script>

<style type="text/css">
#ccm-permissions-access-entity-wrapper .ccm-activate-date-time {margin-right: 8px;}
</style>