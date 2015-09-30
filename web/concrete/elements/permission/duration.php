<?php
use Concrete\Core\Permission\Duration;

defined('C5_EXECUTE') or die("Access Denied.");

$repeats = array(
    ''        => t('** Options'),
    'daily'   => t('Every Day'),
    'weekly'  => t('Every Week'),
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
    $pdStartDateAllDay = $pd->isStartDateAllDay();
    $pdEndDateAllDay = $pd->isEndDateAllDay();
    $pdRepeatPeriodInt = $pd->getRepeatPeriod();
    $pdRepeatPeriodWeekDays = $pd->getRepeatPeriodWeekDays();
    if ($pdRepeatPeriodInt === $pd::REPEAT_DAILY) {
        $pdRepeatPeriod = 'daily';
        $pdRepeatPeriodDaysEvery = $pd->getRepeatPeriodEveryNum();
    }
    if ($pdRepeatPeriodInt === $pd::REPEAT_WEEKLY) {
        $pdRepeatPeriod = 'weekly';
        $pdRepeatPeriodWeeksEvery = $pd->getRepeatPeriodEveryNum();
    }
    if ($pdRepeatPeriodInt === $pd::REPEAT_MONTHLY) {
        $pdRepeatPeriod = 'monthly';
        $pdRepeatPeriodMonthsEvery = $pd->getRepeatPeriodEveryNum();
    }
    $pdRepeatPeriodMonthsRepeatLastDay = $pd->getRepeatMonthLastWeekday();
    $rmb = $pd->getRepeatMonthBy();
    if ($rmb) {
        if ($rmb === Duration::MONTHLY_REPEAT_MONTHLY) {
            $pdRepeatPeriodMonthsRepeatBy = 'month';
        } elseif ($rmb === Duration::MONTHLY_REPEAT_WEEKLY) {
            $pdRepeatPeriodMonthsRepeatBy = 'week';
        } elseif ($rmb === Duration::MONTHLY_REPEAT_LAST_WEEKDAY) {
            $pdRepeatPeriodMonthsRepeatBy = 'lastweekday';
        }
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

    <div class="form-group">
        <label for="pdStartDate_activate" class="control-label"><?=tc('Start date', 'From')?></label>
        <div class="">
            <?= $dt->datetime('pdStartDate', $pdStartDate, true); ?>
            <div class="checkbox"><label><?= $form->checkbox('pdStartDateAllDayActivate', 1, $pdStartDateAllDay) ?> <?= t(
                    "All Day") ?></label>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="pdEndDate_activate" class="control-label"><?=tc('End date', 'To')?></label>
        <div class="">
            <?= $dt->datetime('pdEndDate', $pdEndDate, true); ?>
            <div class="checkbox"><label><?= $form->checkbox('pdEndDateAllDayActivate', 1, $pdEndDateAllDay) ?> <?= t(
                        "All Day") ?></label></div>
        </div>
    </div>

</div>

<div id="ccm-permissions-access-entity-repeat" style="display: none">

    <div class="form-group">
        <div class="">
            <div class="checkbox"><label><?= $form->checkbox('pdRepeat', 1, $pdRepeats) ?> <?= t('Repeat...') ?></label></div>
        </div>
    </div>

</div>

<div id="ccm-permissions-access-entity-repeat-selector" style="display: none">


    <div class="form-group">
        <label for="pdRepeatPeriod" class="control-label"><?=t('Repeats')?></label>
        <div class="">
            <?= $form->select('pdRepeatPeriod', $repeats, $pdRepeatPeriod) ?>
        </div>
    </div>

    <div id="ccm-permissions-access-entity-dates-repeat-daily" style="display: none">

        <div class="form-group">
            <label for="pdRepeatPeriodDaysEvery" class="control-label"><?=t('Repeat every')?></label>
            <div class="">
                <div class="form-inline">
                <?= $form->select(
                    'pdRepeatPeriodDaysEvery',
                    $repeatDays,
                    $pdRepeatPeriodDaysEvery,
                    array('style' => 'width: 60px')) ?>
                <?= t('days') ?>
                </div>
            </div>
        </div>

    </div>

    <div id="ccm-permissions-access-entity-dates-repeat-monthly" style="display: none">


        <div class="form-group">
            <label for="pdRepeatPeriodMonthsRepeatBy" class="control-label"><?=t('Repeat By')?></label>
            <div class="">
                <div class="radio"><label><?= $form->radio(
                    'pdRepeatPeriodMonthsRepeatBy',
                    'month',
                    $pdRepeatPeriodMonthsRepeatBy) ?> <?= t(
                    'Day of Month')
                ?></label>
                </div>
                <div class="radio"><label><?= $form->radio(
                            'pdRepeatPeriodMonthsRepeatBy',
                            'week',
                            $pdRepeatPeriodMonthsRepeatBy) ?> <?= t('Day of Week') ?></label>
                </div>
                <div class="radio">
                    <label>
                        <?= $form->radio(
                            'pdRepeatPeriodMonthsRepeatBy',
                            'lastweekday',
                            $pdRepeatPeriodMonthsRepeatBy) ?> <?= t('The last ') ?>
                        <select name="pdRepeatPeriodMonthsRepeatLastDay">
                            <option value="0" <?= $pdRepeatPeriodMonthsRepeatLastDay == 0 ? 'selected' : '' ?>><?= t('Sunday') ?></option>
                            <option value="1" <?= $pdRepeatPeriodMonthsRepeatLastDay == 1 ? 'selected' : '' ?>><?= t('Monday') ?></option>
                            <option value="2" <?= $pdRepeatPeriodMonthsRepeatLastDay == 2 ? 'selected' : '' ?>><?= t('Tuesday') ?></option>
                            <option value="3" <?= $pdRepeatPeriodMonthsRepeatLastDay == 3 ? 'selected' : '' ?>><?= t('Wednesday') ?></option>
                            <option value="4" <?= $pdRepeatPeriodMonthsRepeatLastDay == 4 ? 'selected' : '' ?>><?= t('Thursday') ?></option>
                            <option value="5" <?= $pdRepeatPeriodMonthsRepeatLastDay == 5 ? 'selected' : '' ?>><?= t('Friday') ?></option>
                            <option value="6" <?= $pdRepeatPeriodMonthsRepeatLastDay == 6 ? 'selected' : '' ?>><?= t('Saturday') ?></option>
                        </select>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="pdRepeatPeriodMonthsEvery" class="control-label"><?=t('Repeat every')?></label>
            <div class="">
                <div class="form-inline">
                    <?= $form->select(
                        'pdRepeatPeriodMonthsEvery',
                        $repeatMonths,
                        $pdRepeatPeriodMonthsEvery,
                        array('style' => 'width: 60px')) ?>
                    <?= t('months') ?>
                </div>
            </div>
        </div>

    </div>


    <div id="ccm-permissions-access-entity-dates-repeat-weekly" style="display: none">


        <div id="ccm-permissions-access-entity-dates-repeat-weekly-dow" style="display: none">

            <div class="form-group">
                <label class="control-label"><?= t('On') ?></label>
                <div class="">
                <?
                foreach (\Punic\Calendar::getSortedWeekdays('wide') as $weekDay) {
                    ?>
                    <div class="checkbox"><label><input
                                <?php if (in_array($weekDay['id'], $pdRepeatPeriodWeekDays)) { ?>checked="checked" <?php } ?>
                                type="checkbox" name="pdRepeatPeriodWeeksDays[]" value="<?= $weekDay['id'] ?>"/> <?= h(
                                $weekDay['name']) ?></label></div>
                <?
                } ?>
                </div>
            </div>

        </div>

        <div class="form-group">
            <label for="pdRepeatPeriodWeeksEvery" class="control-label"><?= t('Repeat every') ?></label>
            <div class="">
                <div class="form-inline">
                    <?= $form->select(
                        'pdRepeatPeriodWeeksEvery',
                        $repeatWeeks,
                        $pdRepeatPeriodWeeksEvery,
                        array('style' => 'width: 60px')) ?>
                    <?= t('weeks') ?>
                </div>
            </div>
        </div>
    </div>

    <div id="ccm-permissions-access-entity-dates-repeat-dates" style="display: none">


        <div class="form-group">
            <label class="control-label"><?= t('Starts On') ?></label>
            <div class="">
                <input type="text" class="form-control" disabled="disabled" value="" name="pdStartRepeatDate"/>
            </div>
        </div>

        <div class="form-group">
            <label for="pdEndRepeatDate" class="control-label"><?= t('Ends') ?></label>
            <div class="">
                <div class="radio"><label><?= $form->radio('pdEndRepeatDate', '', $pdEndRepeatDate) ?> <?= t(
                            'Never') ?></label></div>
                <div class="radio"><label><?= $form->radio('pdEndRepeatDate', 'date', $pdEndRepeatDate) ?> <?= $dt->date(
                            'pdEndRepeatDateSpecific',
                            $pdEndRepeatDateSpecific) ?></label></div>
            </div>
        </div>

    </div>

</div>

<script type="text/javascript">
    ccm_accessEntityCalculateRepeatOptions = function () {
        // get the difference between start date and end date
        if (!$("#pdStartDate_activate").is(':checked')) {
            return false;
        }

        var sdf = ($("#pdStartDate_dt_pub").datepicker('option', 'altFormat'));
        var sdfr = $.datepicker.parseDate(sdf, $("#pdStartDate_dt").val());
        var edf = ($("#pdEndDate_dt_pub").datepicker('option', 'altFormat'));
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
        $('input[name=pdStartRepeatDate]').val($("#pdStartDate_dt_pub").val());
        switch (sdfr.getDay()) {
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

    ccm_accessEntityCheckRepeat = function () {
        if ($('input[name=pdRepeat]').is(':checked')) {
            $("#ccm-permissions-access-entity-repeat-selector").show();
        } else {
            $("#ccm-permissions-access-entity-repeat-selector").hide();
        }
    }

    ccm_accessEntityOnActivateDates = function () {
        if ($("#pdStartDate_activate").is(':checked') || $("#pdEndDate_activate").is(':checked')) {
            ccm_accessEntityCalculateRepeatOptions();
        }
        if ($("#pdStartDate_activate").is(':checked') && $("#pdEndDate_activate").is(':checked')) {
            $("#ccm-permissions-access-entity-repeat").show();
        } else {
            $("#ccm-permissions-access-entity-repeat").hide();
        }
        if ($("#pdStartDate_activate").is(':checked')) {
            $('#pdStartDateAllDayActivate').attr('disabled', false);
        } else {
            $('input[name=pdStartDateAllDayActivate]').attr('disabled', true);
        }
        if ($("#pdEndDate_activate").is(':checked')) {
            $('#pdEndDateAllDayActivate').attr('disabled', false);
        } else {
            $('input[name=pdEndDateAllDayActivate]').attr('disabled', true);
        }

        if ($("input[name=pdStartDateAllDayActivate]").is(':checked')) {
            $('span#pdStartDate_tw').hide();
        } else {
            $('span#pdStartDate_tw').show();
        }

        if ($("input[name=pdEndDateAllDayActivate]").is(':checked')) {
            $('span#pdEndDate_tw').hide();
        } else {
            $('span#pdEndDate_tw').show();
        }

    }

    ccm_accessEntityOnRepeatPeriodChange = function () {
        $("#ccm-permissions-access-entity-dates-repeat-daily").hide();
        $("#ccm-permissions-access-entity-dates-repeat-weekly").hide();
        $("#ccm-permissions-access-entity-dates-repeat-monthly").hide();
        if ($('select[name=pdRepeatPeriod]').val() != '') {
            $("#ccm-permissions-access-entity-dates-repeat-" + $('select[name=pdRepeatPeriod]').val()).show();
            $("#ccm-permissions-access-entity-dates-repeat-dates").show();
        }
    }

    ccm_accessEntityCalculateRepeatEnd = function () {
        if ($('input[name=pdEndRepeatDate]:checked').val() == 'date') {
            $("#ccm-permissions-access-entity-dates-repeat-dates .ccm-input-date-wrapper input").attr('disabled', false);
        } else {
            $("#ccm-permissions-access-entity-dates-repeat-dates .ccm-input-date-wrapper input").attr('disabled', true);
        }
    }

    $(function () {
        $("#ccm-permissions-access-entity-dates input[type=checkbox]").click(function () {
            ccm_accessEntityOnActivateDates();
        });

        $("select[name=pdRepeatPeriod]").change(function () {
            ccm_accessEntityOnRepeatPeriodChange();
        });

        $("input[name=pdRepeat]").click(function () {
            ccm_accessEntityCheckRepeat();
        });

        $("#ccm-permissions-access-entity-dates span.ccm-input-date-wrapper input, #ccm-permissions-access-entity-dates span.ccm-input-time-wrapper select").change(function () {
            ccm_accessEntityCalculateRepeatOptions();
        });
        $("#ccm-permissions-access-entity-dates-repeat-dates input.ccm-input-date").attr('disabled', true);
        $('input[name=pdEndRepeatDate]').change(function () {
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
    #ccm-permissions-access-entity-dates .ccm-activate-date-time {
        margin-right: 8px;
    }
</style>
