<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?

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
$form = Loader::helper('form');
$dt = Loader::helper('form/date_time');

?>


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
	<li><label><?=$form->radio('pdRepeatPeriodMonthsRepeatBy', 'month', $pdRepeatPeriodMonthsRepeatBy)?> <span><?=t('Day of Month')?></span></label></li>
	<li><label><?=$form->radio('pdRepeatPeriodMonthsRepeatBy', 'week', $pdRepeatPeriodMonthsRepeatBy)?> <span><?=t('Day of Week')?></span></label></li>
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
<? 
$x = 0;
foreach($list['format']['wide'] as $key => $value) { ?>
	<li><label><input <? if (in_array($x, $pdRepeatPeriodWeekDays)) { ?>checked="checked" <? } ?>
	type="checkbox" name="pdRepeatPeriodWeeksDays[]" value="<?=$x?>" /> <span><?=$value?></span></label></li>
	
<?
	$x++;
} ?>
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

<script type="text/javascript">
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
			$("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=0]").attr('checked', true);
			break;
		case 1:
			$("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=1]").attr('checked', true);
			break;
		case 2:
			$("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=2]").attr('checked', true);
			break;
		case 3:
			$("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=3]").attr('checked', true);
			break;
		case 4:
			$("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=4]").attr('checked', true);
			break;
		case 5:
			$("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=5]").attr('checked', true);
			break;
		case 6:
			$("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=6]").attr('checked', true);
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
	ccm_accessEntityOnActivateDates();
	ccm_accessEntityCheckRepeat();	
	ccm_accessEntityOnRepeatPeriodChange();
	ccm_accessEntityCalculateRepeatEnd();
});
</script>

<style type="text/css">
#ccm-permissions-access-entity-wrapper .ccm-activate-date-time {margin-right: 8px;}
</style>