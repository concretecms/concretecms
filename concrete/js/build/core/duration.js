!function (global, $) {
    'use strict';

    function ConcreteDurationSelectorRepetition($element, options) {
        'use strict';
        var my = this;

        options = $.extend({
            'setID': '',
            'repetition': '',
            'template': 'script[data-template=duration-wrapper]',
            'dateFormat': '',
            'allowRepeat': true
        }, options);

        my.options = options;
        my.$element = $element;
        my.setID = options.setID;

        var repetition = my.prepareRepetition(my.options.repetition);

        my.repetition = repetition;

        var _template = _.template($(options.template).html(), {
            repetition: repetition,
            options: my.options
        });
        my.$element.append(_template);

        my.setup();
    }

    function ConcreteDurationSelector($element, options) {
        'use strict';
        var my = this;
        options = $.extend({
            'template': 'script[data-template=duration-wrapper]',
            'dateFormat': '',
            'baseRepetition': {},
            'repetitions': [],
            'allowRepeat': true,
            'allowMultiple': true,
            'namespace': ''
        }, options);

        my.options = options;
        my.$element = $element;

        $.each(my.options.repetitions, function (i, repetition) {
            my.addRepetition(repetition);
        });

        if (my.options.allowMultiple) {
            $element.find('button[data-action=add-duration]').on('click', function() {
                my.addRepetition(my.options.baseRepetition);
            });
        }
    }

    ConcreteDurationSelector.prototype = {

        totalRepetitions: 0,

        addRepetition: function(repetition) {
            var $wrapper = $('<div />'),
                setID = (new Date()).getTime(),
                my = this;

            var object = new ConcreteDurationSelectorRepetition($wrapper, {
                'repetition': repetition,
                'setID': setID,
                'dateFormat': my.options.dateFormat,
                'template': my.options.template,
                'allowRepeat': my.options.allowRepeat,
                'namespace': my.options.namespace
            });
            my.$element.find('div[data-duration-selector]').append(object.getElement());

            if (my.totalRepetitions == 0) {
                object.getElement().find('a[data-delete=duration]').hide();
            }

            my.totalRepetitions++;

            object.getElement().on('click', 'a[data-delete=duration]', function(e) {
                object.getElement().remove();
                my.totalRepetitions--;
            });

        }
    },

    ConcreteDurationSelectorRepetition.prototype = {

        getElement: function() {
            var my = this;
            return my.$element;
        },

        getSetID: function() {
            var my = this;
            return my.setID;
        },

        prepareRepetition: function (repetition) {
            var my = this,
                momentStartDate = moment(repetition.pdStartDateTimestamp * 1000).tz(
                    repetition.timezone.timezone
                ),
                round = repetition.repetitionID == 0;

            // if this is a new repetition (ID == 0) then we round the starting date.
            // otherwise we accept what is passed exactly

            repetition.setID = my.setID;
            repetition.pdStartDate = momentStartDate.format('YYYY-MM-DD');
            repetition.pdStartDateSelectTime = my.getTimeFromDate(momentStartDate, round);
            repetition.pdEndDateSelectTime = false;
            if (repetition.pdEndDateTimestamp) {
                var momentEndDate = moment(repetition.pdEndDateTimestamp * 1000).tz(
                    repetition.timezone.timezone
                );
                repetition.pdEndDate = momentEndDate.format('YYYY-MM-DD');
                repetition.pdEndDateSelectTime = my.getTimeFromDate(momentEndDate);
            }
            return repetition;
        },

        setupTimes: function () {
            var my = this;
            my.$element.find('select[data-select=start-time]').selectize({
                onChange: function (value) {
                    my.calculateEndDate();
                }
            });
            my.$element.find('select[data-select=end-time]').selectize({
                copyClassesToDropdown: false,
            });
        },

        getSelectedEndDate: function () {
            var my = this;
            var edf = (my.$element.find("input[name=" + my.options.namespace + "_pdEndDate_pub_" + my.getSetID() + "]").datepicker('option', 'altFormat'));
            var endDate = my.$element.find("input[name=" + my.options.namespace + "_pdEndDate_" + my.getSetID() + "]").val();
            if (endDate) {
                var edfr = $.datepicker.parseDate(edf, endDate);
                var endTime = my.$element.find('select[name=' + my.options.namespace + '_pdEndDateSelectTime_' + my.getSetID() + ']').val();
                if (endTime) {
                    var eh = endTime.split(/:/gi)[0];
                    var em = endTime.split(/:/gi)[1].replace(/\D/g, '');
                    if (endTime.match('/pm/i') && eh < 12) {
                        eh = parseInt(eh) + 12;
                    }
                    return new Date(edfr.getFullYear(), edfr.getMonth(), edfr.getDate(), eh, em, 0);
                }
            }

        },

        getSelectedStartDate: function () {
            var my = this;
            var sdf = (my.$element.find("input[name=" + my.options.namespace + "_pdStartDate_pub_" + my.getSetID() + "]").datepicker('option', 'altFormat'));
            var startDate = my.$element.find("input[name=" + my.options.namespace + "_pdStartDate_" + my.getSetID() + "]").val();
            if (startDate) {
                var sdfr = $.datepicker.parseDate(sdf, startDate);
                var startTime = my.$element.find('select[name=' + my.options.namespace + '_pdStartDateSelectTime_' + my.getSetID() + ']').val();
                if (startTime) {
                    var sh = startTime.split(/:/gi)[0];
                    var sm = startTime.split(/:/gi)[1].replace(/\D/g, '');
                    if (startTime.match(/pm/i) && sh < 12) {
                        sh = parseInt(sh) + 12;
                    } else if (startTime.match(/am/i) && sh == 12) {
                        sh = 0;
                    }
                    return new Date(sdfr.getFullYear(), sdfr.getMonth(), sdfr.getDate(), sh, sm, 0);
                }

            }
        },

        getTimeFromDate: function (momentDate, round) {
            var minutes = momentDate.minutes();
            var hours, pm;

            if (momentDate.hours() == 0) {
                hours = 12;
                pm = 'am';
            } else if (momentDate.hours() > 11) {
                pm = 'pm';
                if (momentDate.hours() > 12) {
                    hours = momentDate.hours() - 12;
                } else {
                    hours = momentDate.hours();
                }
            } else {
                hours = momentDate.hours();
                pm = 'am';
            }

            if (minutes < 10) {
                minutes = '0' + minutes;
            }

            if (round) {
                var selectedTime = hours +  ':00' + pm;
                if (minutes > 29) {
                    var selectedTime = hours + ':30' + pm;
                }
            } else {
                var selectedTime = hours + ':' + minutes + pm;
            }

            return selectedTime;
        },

        calculateEndDate: function () {
            var my = this;
            var startDate = my.getSelectedStartDate();
            if (!startDate) {
                return;
            }
            var endDate = startDate;
            var format = my.$element.find("input[name=" + my.options.namespace + "_pdStartDate_pub_" + my.getSetID() + "]").datepicker('option', 'dateFormat');
            endDate.setTime(startDate.getTime() + (1 * 60 * 60 * 1000)); // one hour

            var endDateFormatted = $.datepicker.formatDate(format, endDate),
                endTime = my.getTimeFromDate(moment(endDate), false);

            my.$element.find("input[name=" + my.options.namespace + "_pdEndDate_pub_" + my.getSetID() + "]").datepicker('setDate', endDateFormatted);

            var $selectize = my.$element.find('select[name=' + my.options.namespace + '_pdEndDateSelectTime_' + my.getSetID() + ']').selectize();
            $selectize[0].selectize.setValue(endTime);
        },

        setupDates: function () {
            var my = this;

            my.$element.find('input[name=' + my.options.namespace + '_pdStartDate_pub_' + my.getSetID() + ']').datepicker({
                dateFormat: my.options.dateFormat,
                altFormat: 'yy-mm-dd',
                altField: my.$element.find('input[name=' + my.options.namespace + '_pdStartDate_' + my.getSetID() + ']'),
                changeYear: true,
                showAnim: 'fadeIn',
                yearRange: 'c-100:c+10'
            });

            my.$element.find('input[name=' + my.options.namespace + '_pdEndDate_pub_' + my.getSetID() + ']').datepicker({
                dateFormat: my.options.dateFormat,
                altFormat: 'yy-mm-dd',
                altField: my.$element.find('input[name=' + my.options.namespace + '_pdEndDate_' + my.getSetID() + ']'),
                changeYear: true,
                showAnim: 'fadeIn',
                yearRange: 'c-100:c+10'
            });

            my.$element.find('input[name=' + my.options.namespace + '_pdEndRepeatDateSpecific_pub_' + my.getSetID() + ']').datepicker({
                dateFormat: my.options.dateFormat,
                altFormat: 'yy-mm-dd',
                altField: my.$element.find('input[name=' + my.options.namespace + '_pdEndRepeatDateSpecific_' + my.getSetID() + ']'),
                changeYear: true,
                showAnim: 'fadeIn',
                yearRange: 'c-100:c+10'
            });

            my.$element.find('input[name=' + my.options.namespace + '_pdStartDate_pub_' + my.getSetID() + ']').datepicker('setDate', my.getSelectedStartDate());
            my.$element.find('input[name=' + my.options.namespace + '_pdEndDate_pub_' + my.getSetID() + ']').datepicker('setDate', my.getSelectedEndDate());

            var endDateSpecific = my.$element.find('input[name=' + my.options.namespace + '_pdEndRepeatDateSpecific_pub_' + my.getSetID() + ']').val();
            if (endDateSpecific) {
                var momentEndDateSpecific = moment(endDateSpecific).tz(
                    my.options.repetition.timezone.timezone
                );
                my.$element.find('input[name=' + my.options.namespace + '_pdEndRepeatDateSpecific_pub_' + my.getSetID() + ']').datepicker('setDate', momentEndDateSpecific.toDate());
            }

            my.$element.find('input[name=' + my.options.namespace + '_pdStartDate_pub_' + my.getSetID() + ']').datepicker({
                onSelect: function () {
                    $(this).trigger('change');
                }
            });
            my.$element.find('input[name=' + my.options.namespace + '_pdStartDate_pub_' + my.getSetID() + ']').on('change', function () {
                my.$element.find('input[name=' + my.options.namespace + '_pdEndDate_pub_' + my.getSetID() + ']').datepicker('setDate', $(this).val());
            });
        },

        setupRepeatOptions: function () {
            var my = this;
            my.$element.find("div[data-wrapper=duration-repeat] input[type=checkbox]").click(function () {
                my.onActivateDates();
            });

            my.$element.find("select[name=" + my.options.namespace + "_pdRepeatPeriod_" + my.getSetID() + "]").change(function () {
                my.onRepeatPeriodChange();
            });

            my.$element.find("input[name=" + my.options.namespace + "_pdRepeat_" + my.getSetID() + "]").click(function () {
                my.checkRepeat();
            });

            my.$element.find("div[data-wrapper=duration-repeat-dates] input.ccm-input-date").attr('disabled', true);
            my.$element.find('input[name=' + my.options.namespace + '_pdEndRepeatDate_' + my.getSetID() + ']').change(function () {
                my.calculateRepeatEnd();
            });

        },

        calculateRepeatOptions: function () {

            var my = this;
            var startDate = my.getSelectedStartDate();
            var endDate = my.getSelectedEndDate();

            var difference = ((endDate.getTime() / 1000) - (startDate.getTime() / 1000));
            var $wrapper = my.$element.find("div[data-wrapper=duration-repeat-weekly-dow]");

            if (difference >= 60 * 60 * 24) {
                my.$element.find('select[name=' + my.options.namespace + '_pdRepeatPeriod_' + my.getSetID() + '] option[value=daily]').prop('disabled', true);
                $wrapper.hide();
            } else {
                my.$element.find('select[name=' + my.options.namespace + '_pdRepeatPeriod_' + my.getSetID() + '] option[value=daily]').prop('disabled', false);
                $wrapper.show();
            }
            $('input[name=' + my.options.namespace + '_pdStartRepeatDate_' + my.getSetID() + ']').val(my.$element.find("input[name=" + my.options.namespace + "_pdStartDate_pub_" + my.getSetID() + "]").val());

            $wrapper.find('input[type=checkbox]').prop('checked', false);

            switch (startDate.getDay()) {
                case 0:
                    $wrapper.find("input[value=0]").prop('checked', true);
                    break;
                case 1:
                    $wrapper.find("input[value=1]").prop('checked', true);
                    break;
                case 2:
                    $wrapper.find("input[value=2]").prop('checked', true);
                    break;
                case 3:
                    $wrapper.find("input[value=3]").prop('checked', true);
                    break;
                case 4:
                    $wrapper.find("input[value=4]").prop('checked', true);
                    break;
                case 5:
                    $wrapper.find("input[value=5]").prop('checked', true);
                    break;
                case 6:
                    $wrapper.find("input[value=6]").prop('checked', true);
                    break;
            }

        },

        checkRepeat: function () {
            var my = this;
            if (my.$element.find('input[name=' + my.options.namespace + '_pdRepeat_' + my.getSetID() + ']').is(':checked')) {
                my.$element.find('div[data-wrapper=duration-repeat-selector]').show();
            } else {
                my.$element.find('div[data-wrapper=duration-repeat-selector]').hide();
            }
        },

        onActivateDates: function () {
            var my = this;
            my.calculateRepeatOptions();

            my.$element.find('div[data-wrapper=duration-repeat]').show();
            my.$element.find('input[name=' + my.options.namespace + '_pdStartDateAllDayActivate_' + my.getSetID() + ']').attr('disabled', false);

            if (my.$element.find("input[name=" + my.options.namespace + "_pdStartDateAllDayActivate_" + my.getSetID() + "]").is(':checked')) {
                my.$element.find('div[data-column=date]').removeClass().addClass('col-sm-12');
                my.$element.find('select[data-select=start-time]').parent().hide();
                my.$element.find('select[data-select=end-time]').parent().hide();
            } else {
                my.$element.find('div[data-column=date]').removeClass().addClass('col-sm-6');
                my.$element.find('select[data-select=start-time]').parent().show();
                my.$element.find('select[data-select=end-time]').parent().show();
            }

        },

        onRepeatPeriodChange: function () {
            var my = this;
            my.$element.find("div[data-wrapper=duration-dates-repeat-daily]").hide();
            my.$element.find("div[data-wrapper=duration-dates-repeat-weekly]").hide();
            my.$element.find("div[data-wrapper=duration-dates-repeat-monthly]").hide();
            var repeatPeriod = my.$element.find('select[name=' + my.options.namespace + '_pdRepeatPeriod_' + my.getSetID() + ']').val();
            if (repeatPeriod != '') {
                my.$element.find("div[data-wrapper=duration-dates-repeat-" + repeatPeriod + "]").show();
                my.$element.find("div[data-wrapper=duration-repeat-dates]").show();
            }
        },

        calculateRepeatEnd: function () {
            var my = this;
            if (my.$element.find('input[name=' + my.options.namespace + '_pdEndRepeatDate_' + my.getSetID() + ']:checked').val() == 'date') {
                my.$element.find("div[data-wrapper=duration-repeat-dates] input[name=" + my.options.namespace + "_pdEndRepeatDateSpecific_pub_" + my.getSetID() + "]").prop('disabled', false);
            } else {
                my.$element.find("div[data-wrapper=duration-repeat-dates] input[name=" + my.options.namespace + "_pdEndRepeatDateSpecific_pub_" + my.getSetID() + "]").prop('disabled', true);
            }
        },

        setup: function () {
            var my = this;
            my.setupDates();
            my.setupTimes();
            my.setupRepeatOptions();
            if (!my.repetition.pdEndDate) {
                my.calculateEndDate();
            }

            my.calculateRepeatOptions();
            my.onActivateDates();
            my.checkRepeat();
            my.onRepeatPeriodChange();
            my.calculateRepeatEnd();
        }
    }

    // jQuery Plugin
    $.fn.concreteDurationSelector = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteDurationSelector($(this), options);
        });
    }

    global.ConcreteDurationSelector = ConcreteDurationSelector;

}(this, $);
