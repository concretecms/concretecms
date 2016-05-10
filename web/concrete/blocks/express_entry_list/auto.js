$(function() {

    function ConcreteExpressEntryListBlockForm(data) {
        'use strict';
        this.data = data;
        this.init(data);
    }

    ConcreteExpressEntryListBlockForm.prototype.initEntitySelector = function() {
        var $source = $('#ccm-tab-content-search'),
            $customizeContainer = $('#ccm-tab-content-results div[data-container=customize-results]'),
            $attributesContainer = $('#ccm-tab-content-search div[data-container=advanced-search]'),
            _searchAttributesTemplate = _.template($('script[data-template=express-attribute-search-list]').html());

        $source.find('select[name=exEntityID]').on('change', function() {
            var exEntityID = $(this).val();
            if (exEntityID) {
                $.concreteAjax({
                    url: $(this).attr('data-action'),
                    data: {'exEntityID': $(this).val()},
                    success: function(r) {
                        $customizeContainer.html(r.customize);
                        $attributesContainer.html(_searchAttributesTemplate({attributes: r.attributes}));
                    }
                });
            }
        });
    }

    ConcreteExpressEntryListBlockForm.prototype.initToggling = function() {
        $('input[type=checkbox][data-options-toggle]').on('change', function() {
            var option = $(this).attr('data-options-toggle');
            if ($(this).is(':checked')) {
                $('[data-options=' + option + ']').show();
            } else {
                $('[data-options=' + option + ']').hide();
            }
        }).trigger('change');
        $('input[type=radio][name=tableStriped]').on('change', function() {
            var value = $('input[type=radio][name=tableStriped]:checked').val();
            if (value == '1') {
                $('[data-options=table-striped]').show();
            } else {
                $('[data-options=table-striped]').hide();
            }
        }).trigger('change');
    }

    ConcreteExpressEntryListBlockForm.prototype.init = function(data) {

        var my = this;
        my.initEntitySelector();
        my.initToggling();


    }


    Concrete.event.bind('block.express_entry_list.open', function(e, data) {

        new ConcreteExpressEntryListBlockForm(data);

    });


});