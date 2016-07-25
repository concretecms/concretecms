$(function() {

    function ConcreteExpressEntryDetailBlockForm(options) {
        'use strict';
        this.options = $.extend({
        }, options);
        this.$container = $('#ccm-block-express-entry-detail-edit');
        this._selectFormTemplate = _.template($('script[data-template=express-attribute-form-list]').html());
        this.init(options);
    }

    ConcreteExpressEntryDetailBlockForm.prototype.initToggling = function() {
        var my = this;
        my.$container.find('select[name=entryMode]').on('change', function() {
            if ($(this).val() == 'S') {
                my.$container.find('div[data-container=express-entry-specific-entry]').show();
            } else {
                my.$container.find('div[data-container=express-entry-specific-entry]').hide();
            }
        }).trigger('change');
    }

    ConcreteExpressEntryDetailBlockForm.prototype.initEntitySelector = function() {
        var my = this;
        my.$container.find('select[name=exEntityID]').on('change', function() {
            var exEntityID = $(this).val();
            if (exEntityID) {
                $.concreteAjax({
                    url: $(this).attr('data-action'),
                    data: {'exEntityID': $(this).val()},
                    success: function(r) {
                        my.$container.find('div[data-container=express-entry-specific-entry]').html(r.selector);
                        my.setForms(r.forms);
                    }
                });
            }
        });
    }

    ConcreteExpressEntryDetailBlockForm.prototype.setForms = function(forms, selected) {
        var my = this,
            $formsContainer = my.$container.find('div[data-container=express-entry-detail-form]');
        $formsContainer.html(my._selectFormTemplate({forms: forms, selected: selected}));
    }

    ConcreteExpressEntryDetailBlockForm.prototype.init = function() {

        var my = this;
        my.initEntitySelector();
        my.initToggling();


    }


    Concrete.event.bind('block.express_entry_detail.open', function(e, data) {

        new ConcreteExpressEntryDetailBlockForm(data);

    });


});