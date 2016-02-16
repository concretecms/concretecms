$(function() {

    Concrete.event.bind('block.express_form.open', function(e, data) {


        var $tabAdd = $('#ccm-tab-content-form-add'),
            $tabEdit = $('#ccm-tab-content-form-edit'),
            $types = $('div[data-group=attribute-types]'),
            $typeData = $('div[data-group=attribute-type-data]'),
            controlTemplate;

        controlTemplate = data.controlTemplate;

        $tabAdd.on('change', 'select[name=type]', function() {
            var id = $(this).val(),
                $form = $('#ccm-tab-content-form-add'),
                $options = $form.find('div[data-attribute-type-id]'),
                $option = $form.find('[data-attribute-type-id=' + id + ']');
            $options.hide();
            if ($option.length) {
                $option.show();
            }
        });

        $tabAdd.on('click', 'button[data-action=add-question]', function() {
            var $form = $tabAdd.find(':input');
            var data = $form.serializeArray();
            jQuery.fn.dialog.showLoader();
            $.concreteAjax({
                url: $tabAdd.attr('data-action'),
                data: data,
                success: function(r) {
                    $tabAdd.find('div.alert-success').show().addClass("animated fadeIn");
                    Concrete.event.publish('block.express_form.add_control', {
                        'control': r,
                        'controlTemplate': controlTemplate
                    });

                    setTimeout(function() {
                        $tabAdd.find('div.alert-success').hide();
                    }, 2000);
                }
            });
        });

        $tabEdit.on('click', 'a[data-action=delete-control]', function() {
            $(this).closest('li').queue(function() {
                $(this).addClass('animated bounceOutLeft');
                $(this).dequeue();
            }).delay(500).queue(function () {
                $(this).remove();
                $(this).dequeue();
            })
        });


        $tabEdit.find('ul').sortable({
            placeholder: "ui-state-highlight",
            axis: "y",
            handle: "i.fa-arrows",
            cursor: "move",
            update: function() {

            }
        });

        $types.find('select').on('change', function() {
            $typeData.html('').hide();
            var value = $(this).val();
            if (value) {
                $types.find('i.fa-refresh').show();
                $.concreteAjax({
                    url: $types.attr('data-action'),
                    data: {'atID': value},
                    loader: false,
                    success: function(r) {
                        $typeData.html(r.content);
                        $typeData.show();
                        _.each(r.assets.css, function(css) {
                            ccm_addHeaderItem(css, 'CSS');
                        });
                        _.each(r.assets.javascript, function(javascript) {
                            ccm_addHeaderItem(javascript, 'JAVASCRIPT');
                        });
                    },
                    complete: function() {
                        $types.find('i.fa-refresh').hide();
                    }
                });
            }
        });
        if (data.controls) {
            _.each(data.controls, function(control) {
                Concrete.event.publish('block.express_form.add_control', {
                    'control': control,
                    'controlTemplate': controlTemplate
                });
            });
        }

    });

    Concrete.event.bind('block.express_form.add_control', function(e, data) {

        var $tabEdit = $('#ccm-tab-content-form-edit'),
            $list = $tabEdit.find('ul');
        $list.append(data.controlTemplate({'control': data.control}));

    });

});