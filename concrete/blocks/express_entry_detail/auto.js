$(function() {

    function ConcreteExpressEntryDetailBlockForm(options) {
        'use strict';
        this.options = $.extend({
            exFormID: 0
        }, options);
        this.$container = $('#ccm-block-express-entry-detail-edit');
        this._selectFormTemplate = _.template($('script[data-template=express-attribute-form-list]').html());
        this.updateAction = this.$container.find('select[name=exEntityID]').attr('data-action');
        this.init(options);
    }

    ConcreteExpressEntryDetailBlockForm.prototype.initToggling = function() {
        var my = this;
        my.$container.find('select[name=entryMode]').on('change', function() {
            if ($(this).val() == 'S') {
                my.$container.find('div[data-container=express-entry-custom-attribute]').hide();
                my.$container.find('div[data-container=express-entry-specific-entry]').show();
                my.$container.find('div[data-container=express-entity]').show();
            } else if ($(this).val() == 'A') {
                my.$container.find('div[data-container=express-entry-specific-entry]').hide();
                my.$container.find('div[data-container=express-entry-custom-attribute]').show();
                my.$container.find('div[data-container=express-entity]').hide();
            } else {
                my.$container.find('div[data-container=express-entry-custom-attribute]').hide();
                my.$container.find('div[data-container=express-entry-specific-entry]').hide();
                my.$container.find('div[data-container=express-entity]').show();
            }
        }).trigger('change');
    }

    ConcreteExpressEntryDetailBlockForm.prototype.updateForms = function(exEntityID, exFormID) {
        var my = this;
        if (exEntityID) {
            $.concreteAjax({
                url: my.updateAction,
                data: {'exEntityID': exEntityID},
                success: function(r) {
                    my.$container.find('div[data-container=express-entry-specific-entry]').html(r.selector);
                    my.setForms(r.forms);
                }
            });
        }
    }

    ConcreteExpressEntryDetailBlockForm.prototype.initEntitySelector = function() {
        var my = this;
        my.$container.find('select[name=exEntityID]').on('change', function() {
            var exEntityID = $(this).val();
            my.updateForms(exEntityID);
        });
        my.$container.find('select[name=exEntryAttributeKeyHandle]').on('change', function() {
            var exEntityID = $(this).find('option:selected').attr('data-entity-id');
            my.updateForms(exEntityID);
        });

        var entryMode = my.$container.find('select[name=entryMode]').val();
        if (entryMode == 'A') {
            my.$container.find('select[name=exEntryAttributeKeyHandle]').trigger('change');
        }
    }

    ConcreteExpressEntryDetailBlockForm.prototype.setForms = function(forms) {
        var my = this,
            $formsContainer = my.$container.find('div[data-container=express-entry-detail-form]');
        $formsContainer.html(my._selectFormTemplate({forms: forms, exFormID: my.options.exFormID}));
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