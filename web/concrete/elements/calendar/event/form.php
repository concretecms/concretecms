<?php
use Concrete\Core\Calendar\Event\EventOccurrence;

/** @var EventOccurrence $occurrence */
if ($occurrence) {
    $event = $occurrence->getEvent();
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
<fieldset>
    <legend><?=t('Date & Time')?></legend>
    <?= \Loader::element('permission/duration', array('pd' => $event ? $event->getRepetition() : null)); ?>
</fieldset>

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
