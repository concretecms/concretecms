<div class="ccm-ui">
    <form class="ccm-event-add form form-horizontal">

        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">
                <?= t('Name') ?>
            </label>

            <div class="col-sm-10">
                <input type="text" class="form-control" placeholder="Name" name="name">
            </div>
        </div>
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">
                <?= t('Description') ?>
            </label>

            <div class="col-sm-10">
                <input type="text" class="form-control" placeholder="Description" name="description">
            </div>
        </div>
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">
                <?= t('Schedule') ?>
            </label>

            <div class="col-sm-10">
                <button class="ccm-event-add-repetition btn btn-default btn-small">
                    <i class="fa fa-clock-o"></i>
                </button>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-2">
            </div>
            <div class="col-sm-10">
                <button class="ccm-event-add-submit btn btn-primary">
                    <?= t('Submit') ?>
                </button>
            </div>
        </div>
    </form>

</div>
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
