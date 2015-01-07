<?php use Concrete\Core\Calendar\Event\Event;
use Concrete\Core\Calendar\Event\EventList;

defined('C5_EXECUTE') or die("Access Denied.");
/** @var EventList $event_list */
$token = \Core::make('helper/validation/token');
$view = \View::getInstance();
?>

<div class="ccm-dashboard-content-full">

    <form class="form-inline ccm-search-fields">
        <div class="ccm-search-fields-submit text-right">

            <button class="add-button btn btn-primary">
                <i class="fa fa-plus"></i>
                <?= t('Add Event') ?>
            </button>

        </div>
    </form>

    <div class="table-responsive">
        <table class="ccm-search-results-table">
            <thead>
            <tr>
                <th class="<?= $event_list->getSortClassName('eventID') ?>">
                    <a href="<?= $event_list->getSortURL('eventID', 'asc') ?>"><?= t('ID') ?></a>
                </th>
                <th class="<?= $event_list->getSortClassName('name') ?>">
                    <a href="<?= $event_list->getSortURL('name', 'asc') ?>"><?= t('Name') ?></a>
                </th>
                <th class="<?= $event_list->getSortClassName('description') ?>">
                    <a href="<?= $event_list->getSortURL('description', 'asc') ?>"><?= t('Description') ?></a>
                </th>
                <th>
                    <span><?= t('Start Date') ?></span>
                </th>
                <th>
                    <span><?= t('End Date') ?></span>
                </th>
                <th>
                    <span><?= t('Repeats') ?></span>
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            /** @var Event $event */
            foreach ($event_list->getPagination()->getIterator() as $event) {
                $delete_action = $view->action('delete', $event->getID(), $token->generate('delete_event'));
                $start_time = strtotime($event->getRepetition()->getStartDate());
                $end_time = strtotime($event->getRepetition()->getEndDate());
                ?>
                <tr data-event-id="<?= $event->getID() ?>">
                    <td>
                        <?= $event->getID() ?>
                    </td>
                    <td>
                        <?= $event->getName() ?>
                    </td>
                    <td>
                        <?= $event->getDescription() ?>
                    </td>
                    <td>
                        <?= $event->getRepetition()->isStartDateAllDay() ? date('Y-m-d ', $start_time) . t('All day') : date('Y-m-d H:i:s', $start_time) ?>
                    </td>
                    <td>
                        <?= $event->getRepetition()->isEndDateAllDay() ? date('Y-m-d ', $end_time) . t('All day') : date('Y-m-d H:i:s', $end_time) ?>
                    </td>
                    <td>
                        <?= $event->getRepetition()->repeats() ? t('Yes') : t('No') ?>
                    </td>
                    <td style="min-width:165px" class="text-right">
                        <button class="btn btn-default btn-small repetition">
                            <i class="fa fa-clock-o"></i>
                        </button>
                        <a href="<?= $delete_action ?>" class="btn btn-danger btn-small delete-event">
                            <i class="fa fa-trash"></i> <?= t('Delete') ?>
                        </a>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>

    <!-- END Body Pane -->
    <?= $event_list->getPagination()->renderDefaultView() ?>

</div>

<script>
    (function() {

        $('button.repetition').click(function() {
            var me = $(this), id = me.closest('tr').data('event-id');
            $.getJSON('<?= $view->action('duration_overlay') ?>', {
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
                        $.post('<?= $view->action('update_repetition') ?>', {
                            id: id,
                            repetition: JSON.stringify(data.repetition),
                            token: '<?= $token->generate('update_repetition') ?>'
                        }, function (result) {
                            console.log(arguments)
                        });
                    });
                });

                $.fn.dialog.open({
                    element: element.get(0)
                });
            });
        });

        $('button.add-button').click(function() {
            var me = $(this);

            Concrete.event.bind('EventAddOpen', function(event, event_add) {
                Concrete.event.bind('EventAddSubmit', function(event, object) {
                    console.log(arguments);
                    if (object === event_add) {
                        $.post('<?= $view->action('add_event') ?>', {
                            token: '<?= $token->generate('add_event') ?>',
                            event: JSON.stringify(object.event)
                        }, function(data) {
                            if (data.error) {
                                alert('Error: ' + data.error);
                            } else {
                                window.location = '<?= $this->action('') ?>';
                                $.fn.dialog.closeTop();
                            }
                        });
                    }
                })
            });

            $.getJSON('<?= $view->action('add_overlay') ?>', function (result) {
                if (result.error) {
                    alert('Error: ' + result.error.message);
                    return;
                }
                var element = $('<div />');
                element.append(result.result);

                $.fn.dialog.open({
                    element: element.get(0)
                });
            });

            return false;
        });

        $('.delete-event').click(function() {
            if (!window.confirm('<?= t('Are you sure you want to delete this event?') ?>')) {
                return false;
            }
        });

    }());
</script>
