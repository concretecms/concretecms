<div class="ccm-ui">
    <form class="ccm-events-repetition">
        <div><div>
        <?= \Loader::element('permission/duration', array('pd' => $event ? $event->getRepetition() : null)); ?>
        <div class="form-group">
            <div class="text-right">
                <button class="ccm-events-repetition-submit btn btn-default"><?= t('Submit') ?></button>
            </div>
        </div>
    </form>
</div>
<script>
    (function() {
        function Repetition(){
            this.startDate = null;
            this.endDate = null;
            this.startDateAllDay = false;
            this.endDateAllDay = false;
            this.repeatPeriod = 0;
            this.repeatPeriodWeekDays = [];
            this.repeatEveryNum = 1;
            this.repeatMonthBy = null;
            this.repeatPeriodEnd = null;
        }

        function EventRepetition() {
            this.init();
            this.repetition = new Repetition;

            Concrete.event.fire('EventRepetitionOpen', this);
        }

        EventRepetition.prototype = {

            init: function() {
                var my = this,
                    form = $('.ccm-events-repetition');

                form.submit(function (e) {
                    e.preventDefault();
                    e.stopPropagation();


                    var me = $(this),
                        serialized_data = me.serializeArray(),
                        data = {};

                    _(serialized_data).each(function(element) {
                        data[element['name']] = element['value'];
                    });
                    console.log(data);

                    var _data = _(data);
                    if (_data.has('pdStartDate_activate') && data.pdStartDate_activate.toLowerCase() == 'on') {

                        my.repetition.startDate = data.pdStartDate_dt;

                        if (!_data.has('pdStartDateAllDayActivate')) {
                            my.repetition.startDate += ' ' + data.pdStartDate_h + ':' + data.pdStartDate_m + ' ' + data.pdStartDate_a;
                        } else {
                            my.repetition.startDateAllDay = true;
                        }

                        if (_data.has('pdEndDate_activate') && data.pdEndDate_activate.toLowerCase() == 'on') {
                            my.repetition.endDate = data.pdEndDate_dt;

                            if (!_data.has('pdEndDateAllDayActivate')) {
                                my.repetition.endDate += ' ' + data.pdEndDate_h + ':' + data.pdEndDate_m + ' ' + data.pdEndDate_a;
                            } else {
                                my.repetition.endDateAllDay = true;
                            }
                        }

                        if (_data.has('pdRepeat') && data.pdRepeat == 1 && _data.has('pdRepeatPeriod') && data.pdRepeatPeriod.length) {

                            if (_data.has('pdEndRepeatDateSpecific')) {
                                my.repetition.repeatPeriodEnd = data.pdEndRepeatDateSpecific;
                            }

                            switch (data.pdRepeatPeriod) {
                                case 'daily':
                                    my.repetition.repeatPeriod = 1;
                                    my.repetition.repeatEveryNum = data.pdRepeatPeriodDaysEvery;
                                    break;
                                case 'weekly':
                                    my.repetition.repeatPeriod = 2;
                                    my.repetition.repeatEveryNum = data.pdRepeatPeriodWeeksEvery;

                                    form.find('input[name^=pdRepeatPeriodWeeksDays]').filter(':checked').each(function() {
                                        my.repetition.repeatPeriodWeekDays.push($(this).val());
                                    });

                                    break;
                                case 'monthly':
                                    my.repetition.repeatPeriod = 3;
                                    my.repetition.repeatEveryNum = data.pdRepeatPeriodMonthsEvery;
                                    my.repetition.repeatMonthBy = data.pdRepeatPeriodMonthsRepeatBy == 'week' ? 1 : 2;
                                    break;
                            }
                        }

                        Concrete.event.fire('EventRepetitionSubmit', my);
                    } else {
                        alert('You must provide a start date.');
                    }
                    return false;
                });
            }
        };

        new EventRepetition();
    }())
</script>
