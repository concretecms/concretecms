
<?php
use Concrete\Core\Calendar\Event\EventOccurrence;
?>
<div class="ccm-event-form">
    <?php
    /** @var EventOccurrence $occurrence */
    if ($occurrence) {
        $event = $occurrence->getEvent();
        if ($event->getRepetition()->repeats()) {
            ?>
            <fieldset>
                <div class="form-group">
                    <div class="radio">
                        <label>
                            <input type="radio" name="edit_type" value="all" checked/> <?= t('All occurrences') ?>
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="edit_type" value="local"/> <?= t('Just this occurrence') ?>
                        </label>
                    </div>
                </div>
            </fieldset>

        <?php
        }
    }
    ?>

    <fieldset>
        <legend><?=t('Basics')?></legend>
        <div class="form-group">
            <label for="name" class="control-label">
                <?= t('Name') ?>
            </label>

            <input type="text" class="form-control" placeholder="Name" name="name" value="<?= $event ? $event->getName() : '' ?>">
        </div>
        <div class="form-group">
            <label for="name" class="control-label">
                <?= t('Description') ?>
            </label>

            <textarea class="form-control" rows="3" placeholder="Description" name="description"><?= $event ? $event->getDescription() : '' ?></textarea>
        </div>
    </fieldset>
    <?php
    if ($occurrence) {
        ?>
        <fieldset class="date-time" style="display:none">
            <legend><?= t('Date &amp; Time') ?></legend>
            <?php
            $form = \Core::make('helper/form');
            $dt = \Core::make('helper/form/date_time');

            $pdStartDate = date('Y-m-d H:i:s', $occurrence->getStart());
            $pdEndDate = date('Y-m-d H:i:s', $occurrence->getEnd());
            ?>
            <div id="ccm-permissions-access-entity-dates">

                <div class="form-group">
                    <label for="pdStartDate_activate" class="control-label"><?= tc('Start date', 'From') ?></label>

                    <div class="">
                        <?= $dt->datetime('pdOccurrenceStartDate', $pdStartDate, true); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="pdEndDate_activate" class="control-label"><?= tc('End date', 'To') ?></label>

                    <div class="">
                        <?= $dt->datetime('pdOccurrenceEndDate', $pdEndDate, true); ?>
                    </div>
                </div>

            </div>
        </fieldset>
        <?php
    }
    ?>
    <fieldset class="repeat-date-time">
        <legend><?=t('Date & Time')?></legend>
        <?= \Loader::element('permission/duration', array('pd' => $event ? $event->getRepetition() : null)); ?>
    </fieldset>
</div>

<script>
    _.defer(function() {
        var radios = $("input[name='edit_type']"),
            local = $('fieldset.date-time'),
            all = $('fieldset.repeat-date-time'),
            delete_local = $('a.delete-local'),
            delete_all = $('a.delete-all');

        radios.closest('form').change(function() {
            if (radios.filter(':checked').val() === 'local') {
                local.show();
                all.hide();

                delete_local.show();
                delete_all.hide();
            } else {
                local.hide();
                all.show();

                delete_local.hide();
                delete_all.show();
            }
        });

        delete_local.click(function() {
            return confirm('<?= t('Are you sure you want to delete this occurrence?') ?> ');
        });
        delete_all.click(function() {
            return confirm('<?= t('Are you sure you want to delete this event and all occurrences?') ?> ');
        });
    });
</script>

<?
    $attributes = \Concrete\Core\Attribute\Key\EventKey::getList();
    $af = Core::make('helper/form/attribute');
    if (is_object($event)) {
        $af->setAttributeObject($event);
    }
    if (count($attributes)) { ?>
    <fieldset>
        <legend><?=t("Custom Attributes")?></legend>
        <?
        foreach($attributes as $ak) {
            echo $af->display($ak);
        }
        ?>
    </fieldset>
<? } ?>

    <? /*
<script>
    (function () {

        function EventAdd() {
            this.event = {
                name: '',
                description: '',
                repetition: {}
            };
            this.init();

            Concrete.event.fire('EventAddOpen', this);
        }

        EventAdd.prototype = {

            init: function () {
                var my = this;
                $('form.ccm-event-add').submit(function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var me = $(this),
                        data = me.serializeArray();

                    _(data).each(function(element) {
                        my.event[element['name']] = element['value'];
                    });

                    Concrete.event.fire('EventAddSubmit', my);
                    return false;
                }).find('.ccm-event-add-repetition').click(function () {
                    var me = $(this), id = me.closest('tr').data('event-id');
                    $.getJSON('<?= \URL::to('dashboard/events/events/duration_overlay') ?>', {
                        id: id
                    }, function (result) {
                        if (result.error) {
                            alert('Error: ' + result.error.message);
                            return;
                        }
                        var element = $('<div />');
                        element.append(result.result);

                        Concrete.event.bind('EventRepetitionOpen', function(event, event_repetition) {
                            Concrete.event.unbind(event);

                            Concrete.event.bind('EventRepetitionSubmit', function(event, data) {
                                if (data === event_repetition) {
                                    Concrete.event.unbind(event);
                                }

                                my.event.repetition = data.repetition;
                                $.fn.dialog.closeTop();
                            });
                        });

                        $.fn.dialog.open({
                            element: element.get(0)
                        });
                    });

                    return false;
                });
            }
        };

        var me = new EventAdd;
    }());
</script>
*/ ?>
