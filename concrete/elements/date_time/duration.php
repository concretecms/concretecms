<?php
use Concrete\Core\Permission\Duration;

defined('C5_EXECUTE') or die("Access Denied.");

$r = \Concrete\Core\Http\ResponseAssetGroup::get();
$r->requireAsset('select2');

if (Config::get('concrete.misc.user_timezones')) {
    $user = new User();
    $userInfo = $user->getUserInfoObject();
    $timezone = $userInfo->getUserTimezone();
} else {
    $timezone = Config::get('app.timezone');
}

$repeats = array(
    '' => t('** Options'),
    'daily' => t('Every Day'),
    'weekly' => t('Every Week'),
    'monthly' => t('Every Month'),
);
$repeatDays = array();
for ($i = 1; $i <= 30; ++$i) {
    $repeatDays[$i] = $i;
}
$repeatWeeks = array();
for ($i = 1; $i <= 30; ++$i) {
    $repeatWeeks[$i] = $i;
}
$repeatMonths = array();
for ($i = 1; $i <= 12; ++$i) {
    $repeatMonths[$i] = $i;
}

$service = Core::make('helper/date');
$now = $service->toDateTime('now', 'user');

$pdStartDate = $now->format('Y-m-d');

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

$now = $service->toDateTime('now', 'user');
$currentHour = $now->format('g');
$currentMinutes = $now->format('i');
$currentAM = $now->format('a');

$selectedStartTime = $currentHour . ':00' . $currentAM;
if ($currentMinutes > 29) {
    $selectedStartTime = $currentHour . ':30' . $currentAM;
}

$selectedEndTime = null;

if (is_object($pd)) {
    $pdStartDate = $pd->getStartDate();
    $pdEndDate = $pd->getEndDate();
    $selectedStartTime = date('g:ia', strtotime($pdStartDate));
    $selectedEndTime = date('g:ia', strtotime($pdEndDate));
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

$values = array(
    '12:00am',
    '12:30am',
    '1:00am',
    '1:30am',
    '2:00am',
    '2:30am',
    '3:00am',
    '3:30am',
    '4:00am',
    '4:30am',
    '5:00am',
    '5:30am',
    '6:00am',
    '6:30am',
    '7:00am',
    '7:30am',
    '8:00am',
    '8:30am',
    '9:00am',
    '9:30am',
    '10:00am',
    '10:30am',
    '11:00am',
    '11:30am',
    '12:00pm',
    '12:30pm',
    '1:00pm',
    '1:30pm',
    '2:00pm',
    '2:30pm',
    '3:00pm',
    '3:30pm',
    '4:00pm',
    '4:30pm',
    '5:00pm',
    '5:30pm',
    '6:00pm',
    '6:30pm',
    '7:00pm',
    '7:30pm',
    '8:00pm',
    '8:30pm',
    '9:00pm',
    '9:30pm',
    '10:00pm',
    '10:30pm',
    '11:00pm',
    '11:30pm',
);


$times = array();
for ($i = 0; $i < count($values); $i++) {
    $value = $values[$i];
    $o = new stdClass;
    $o->id = $value;
    $o->text = $value;
    $times[] = $o;

}


?>

<div id="ccm-permission-access-entity-time-settings-wrapper">

<div id="ccm-permissions-access-entity-dates">

    <div class="form-inline">
        <div class="form-group">
            <?= $dt->date('pdStartDate', $pdStartDate); ?>
        </div>
        <div class="form-group" id="pdStartDate_tw">
            <input type="hidden" data-select="time" name="pdStartDateSelectTime" style="width: 100%" value="<?=$selectedStartTime?>"/>
        </div>
        <div class="form-inline-separator"><i class="fa fa-long-arrow-right"></i></div>
        <div class="form-group">
            <?= $dt->date('pdEndDate', $pdEndDate); ?>
        </div>
        <div class="form-group" id="pdEndDate_tw">
            <input type="hidden" data-select="time" name="pdEndDateSelectTime" style="width: 100%" value="<?=$selectedEndTime?>"/>
        </div>
    </div>


    <style type="text/css">
        div.form-inline div.form-group input.ccm-input-date {
            width: 95px;
        }
        div.form-inline #pdStartDate_tw, div.form-inline #pdEndDate_tw {
            width: 100px;
        }
        div.form-inline-separator {
            font-size: 18px;
            color: #999;
            margin-left: 20px;
            margin-right: 20px;
            display: inline-block;
        }

        div.ccm-select2-flat {
            min-width: 100px;
        }
    </style>

    <script type="text/javascript">
        $(function () {

            $('input[data-select=time]').select2({

                createSearchChoice: function (term, data) {
                    if ($(data).filter(function () {
                            return this.text.localeCompare(term) === 0;
                        }).length === 0) {
                        return {id: term, text: term};
                    }
                },
                initSelection: function(element, callback) {
                    return callback({id: element.val(), text: element.val()});
                },

                dropdownCssClass: 'ccm-ui ccm-select2-flat',
                multiple: false,
                data: <?=json_encode($times)?>
            }).on('change', function() {
                var name = $(this).attr('name');
                if (name == 'pdStartDateSelectTime') {
                    ccm_durationCalculateEndDate();
                }
            });
        });
    </script>

</div>

<div class="form-group-highlight">

<div id="ccm-permissions-access-entity-repeat" style="display: none">

    <div class="form-inline">

    <div class="form-group" style="width: 100px">
        <label><?= $form->checkbox('pdStartDateAllDayActivate', 1,
                    $pdStartDateAllDay) ?> <?= t(
                    "All Day") ?></label>
    </div>
    <div class="form-group">
        <label><?= $form->checkbox('pdRepeat', 1, $pdRepeats) ?> <?= t('Repeat Event') ?></label>
    </div>
    <div class="pull-right text-muted">
        <?=$service->getTimeZoneDisplayName($timezone)?>
    </div>


    </div>
</div>

<div id="ccm-permissions-access-entity-repeat-selector" style="display: none">


    <div class="form-group">
        <label for="pdRepeatPeriod" class="control-label"><?= t('Repeats') ?></label>
        <div class="">
            <?= $form->select('pdRepeatPeriod', $repeats, $pdRepeatPeriod) ?>
        </div>
    </div>

    <div id="ccm-permissions-access-entity-dates-repeat-daily" style="display: none">

        <div class="form-group">
            <label for="pdRepeatPeriodDaysEvery" class="control-label"><?= t('Repeat every') ?></label>
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
            <label for="pdRepeatPeriodMonthsRepeatBy" class="control-label"><?= t('Repeat By') ?></label>
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
                            <option
                                value="0" <?= $pdRepeatPeriodMonthsRepeatLastDay == 0 ? 'selected' : '' ?>><?= t('Sunday') ?></option>
                            <option
                                value="1" <?= $pdRepeatPeriodMonthsRepeatLastDay == 1 ? 'selected' : '' ?>><?= t('Monday') ?></option>
                            <option
                                value="2" <?= $pdRepeatPeriodMonthsRepeatLastDay == 2 ? 'selected' : '' ?>><?= t('Tuesday') ?></option>
                            <option
                                value="3" <?= $pdRepeatPeriodMonthsRepeatLastDay == 3 ? 'selected' : '' ?>><?= t('Wednesday') ?></option>
                            <option
                                value="4" <?= $pdRepeatPeriodMonthsRepeatLastDay == 4 ? 'selected' : '' ?>><?= t('Thursday') ?></option>
                            <option
                                value="5" <?= $pdRepeatPeriodMonthsRepeatLastDay == 5 ? 'selected' : '' ?>><?= t('Friday') ?></option>
                            <option
                                value="6" <?= $pdRepeatPeriodMonthsRepeatLastDay == 6 ? 'selected' : '' ?>><?= t('Saturday') ?></option>
                        </select>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="pdRepeatPeriodMonthsEvery" class="control-label"><?= t('Repeat every') ?></label>
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
                <label class="control-label"><?= tc('Date', 'On') ?></label>
                <div class="">
                    <?php
                    foreach (\Punic\Calendar::getSortedWeekdays('wide') as $weekDay) {
                        ?>
                        <div class="checkbox"><label><input
                                    <?php if (in_array($weekDay['id'], $pdRepeatPeriodWeekDays)) {
                                    ?>checked="checked" <?php
                                }
                                ?>
                                    type="checkbox" name="pdRepeatPeriodWeeksDays[]"
                                    value="<?= $weekDay['id'] ?>"/> <?= h(
                                    $weekDay['name']) ?></label></div>
                        <?php

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
                <div class="radio"><label><?= $form->radio('pdEndRepeatDate', 'date',
                            $pdEndRepeatDate) ?> <?= $dt->date(
                            'pdEndRepeatDateSpecific',
                            $pdEndRepeatDateSpecific) ?></label></div>
            </div>
        </div>

    </div>

</div>

</div>

</div>


<script type="text/javascript">

    ccm_getSelectedStartDate = function() {
        var sdf = ($("#pdStartDate_pub").datepicker('option', 'altFormat'));
        var sdfr = $.datepicker.parseDate(sdf, $("#pdStartDate").val());
        var startTime = $('input[name=pdStartDateSelectTime]').val();
        var sh = startTime.split(/:/gi)[0];
        var sm = startTime.split(/:/gi)[1].replace(/\D/g, '');
        if (startTime.match(/pm/i) && sh < 12) {
            sh = parseInt(sh) + 12;
        }
        return new Date(sdfr.getFullYear(), sdfr.getMonth(), sdfr.getDate(), sh, sm, 0);
    }

    ccm_getSelectedEndDate = function() {
        var edf = ($("#pdEndDate_pub").datepicker('option', 'altFormat'));
        var edfr = $.datepicker.parseDate(edf, $("#pdEndDate").val());
        var endTime = $('input[name=pdEndDateSelectTime]').val();
        if (endTime) {
            var eh = endTime.split(/:/gi)[0];
            var em = endTime.split(/:/gi)[1].replace(/\D/g, '');
            if (endTime.match('/pm/i') && eh < 12) {
                eh = parseInt(eh) + 12;
            }
            return new Date(edfr.getFullYear(), edfr.getMonth(), edfr.getDate(), eh, em, 0);
        }
    }

    ccm_accessEntityCalculateRepeatOptions = function () {

        var startDate = ccm_getSelectedStartDate();
        var endDate = ccm_getSelectedEndDate();

        var difference = ((endDate.getTime() / 1000) - (startDate.getTime() / 1000));

        if (difference >= 60 * 60 * 24) {
            $('select[name=pdRepeatPeriod] option[value=daily]').attr('disabled', true);
            $("#ccm-permissions-access-entity-dates-repeat-weekly-dow").hide();
        } else {
            $('select[name=pdRepeatPeriod] option[value=daily]').attr('disabled', false);
            $("#ccm-permissions-access-entity-dates-repeat-weekly-dow").show();
        }
        $('input[name=pdStartRepeatDate]').val($("#pdStartDate_dt_pub").val());
        switch (startDate.getDay()) {
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
        ccm_accessEntityCalculateRepeatOptions();

        $("#ccm-permissions-access-entity-repeat").show();
        $('#pdStartDateAllDayActivate').attr('disabled', false);
        $('#pdEndDateAllDayActivate').attr('disabled', false);

        if ($("input[name=pdStartDateAllDayActivate]").is(':checked')) {
            $('#pdStartDate_tw').hide();
            $('#pdEndDate_tw').hide();
        } else {
            $('#pdStartDate_tw').show();
            $('#pdEndDate_tw').show();
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

    ccm_durationCalculateEndDate = function() {
        var startDate = ccm_getSelectedStartDate();
        var endDate = startDate;
        var format = $("#pdStartDate_pub").datepicker('option', 'dateFormat');
        endDate.setTime(startDate.getTime() + (1*60*60*1000)); // one hour
        var endDateFormatted = $.datepicker.formatDate(format, endDate);
        var hours = endDate.getHours();
        var pm = 'am';
        var minutes = endDate.getMinutes();
        if (hours == 0) {
            hours = 12;
        }
        if (minutes < 10) {
            minutes = '0' + minutes;
        }
        if (hours > 12) {
            hours = hours - 12;
            pm = 'pm';
        }
        var endTime = hours + ':' + minutes + pm;
        $('#pdEndDate_pub').datepicker('setDate', endDateFormatted);
        $('input[name=pdEndDateSelectTime]').select2('val', endTime);
        $('input[name=pdEndDateSelectTime]').val(endTime);
    }

    $(function () {
        <?php if (!$selectedEndTime) { ?>
            ccm_durationCalculateEndDate();
        <?php } ?>
        $("#ccm-permissions-access-entity-repeat input[type=checkbox]").click(function () {
            ccm_accessEntityOnActivateDates();
        });
        $('#pdStartDate_pub').datepicker({
           onSelect: function() {
               $(this).trigger('change');
           }
        });
        $('#pdStartDate_pub').on('change', function() {
            $('#pdEndDate_pub').datepicker('setDate', $(this).val());
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
