$(function() {

    Concrete.event.bind('block.express_form.open', function(e, data) {


        var $tabAdd = $('#ccm-tab-content-form-add'),
            $tabEdit = $('#ccm-tab-content-form-edit'),
            controlTemplate = _.template($('script[data-template=express-form-form-control]').html()),
            questionTemplate = _.template($('script[data-template=express-form-form-question]').html());

        $tabAdd.find('div[data-view=add-question-inner]').html(questionTemplate({
            'question': '',
            'id': null,
            'isRequired': false,
            'selectedType': '',
            'types': data.types,
            'typeContent': null
        }));

        var $types = $('div[data-group=attribute-types]'),
            $typeData = $('div[data-group=attribute-type-data]');

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

        $tabEdit.on('click', 'button[data-action=update-question]', function() {
            var $form = $tabEdit.find(':input');
            var data = $form.serializeArray();
            jQuery.fn.dialog.showLoader();
            $.concreteAjax({
                url: $tabEdit.attr('data-action'),
                data: data,
                success: function(r) {
                    var $fields = $tabEdit.find('[data-view=form-fields]'),
                        $editQuestion = $tabEdit.find('[data-view=edit-question]'),
                        $editQuestionInner = $tabEdit.find('[data-view=edit-question-inner]');

                    $tabEdit.find('div.alert-success').show().addClass("animated fadeIn");

                    $editQuestion.hide();
                    $fields.show();
                    $editQuestionInner.html('');

                    Concrete.event.publish('block.express_form.update_control', {
                        'control': r,
                        'controlTemplate': controlTemplate
                    });

                    setTimeout(function() {
                        $tabEdit.find('div.alert-success').hide();
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

        $tabEdit.on('click', 'button[data-action=cancel-edit]', function() {
            var $fields = $tabEdit.find('[data-view=form-fields]'),
                $editQuestion = $tabEdit.find('[data-view=edit-question]'),
                $editQuestionInner = $tabEdit.find('[data-view=edit-question-inner]');

            $editQuestion.hide();
            $editQuestionInner.html('');
            $fields.show();
        });


        $tabEdit.on('click', 'a[data-action=edit-control]', function() {
            var $control = $(this).closest('[data-form-control-id]');
            $.concreteAjax({
                url: $control.attr('data-action'),
                data: {'control': $control.attr('data-form-control-id')},
                success: function(r) {
                    var $fields = $tabEdit.find('[data-view=form-fields]'),
                        $editQuestion = $tabEdit.find('[data-view=edit-question]'),
                        $editQuestionInner = $tabEdit.find('[data-view=edit-question-inner]');

                    $fields.hide();
                    $editQuestion.show();

                    $editQuestionInner.html(questionTemplate({
                        'id': r.id,
                        'question': r.question,
                        'isRequired': r.isRequired,
                        'selectedType': r.type,
                        'types': data.types,
                        'typeContent': r.typeContent
                    }));

                    _.each(r.assets.css, function(css) {
                        ccm_addHeaderItem(css, 'CSS');
                    });
                    _.each(r.assets.javascript, function(javascript) {
                        ccm_addHeaderItem(javascript, 'JAVASCRIPT');
                    });
                }
            });
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

    Concrete.event.bind('block.express_form.update_control', function(e, data) {

        var $tabEdit = $('#ccm-tab-content-form-edit'),
            $control = $tabEdit.find('[data-form-control-id=' + data.control.id + ']');
        if ($control) {
            $control.replaceWith(data.controlTemplate({'control': data.control}));
        }

    });


});