<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui">

    <div class="ccm-calendar-wrapper" data-calendar="<?= $calendar->getID() ?>">


    </div>


</div>


<script type="text/javascript">
    $(function () {
        $('div[data-calendar=<?=$calendar->getID()?>]').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,basicWeek,basicDay'
            },
            locale: <?= json_encode(Localization::activeLanguage()); ?>,
            contentHeight: 'auto',
            events: {
                url: '<?=$view->action('get_events')?>',
                data: {
                    'caID': '<?=$calendar->getID()?>'
                }
            },
            eventDataTransform: function(event) {
                if(event.allDay) {
                    event.end = moment(event.end).add(1, 'days')
                }
                return event;
            },
            eventRender: function(event, element) {
                element.attr('href', '#'); // Just to make the pointer nice instead of a text handle.
            },
            eventClick: function(event, jsView, view) {
                ConcreteEvent.publish('CalendarEventSearchDialogSelectEvent', {
                    id: event.id,
                    title: event.title,
                    occurrenceID: event.occurrenceID
                });
            }
        });
    });
</script>
