$(function() {

    function ConcreteExpressEntryListBlockForm(options) {
        'use strict';
        this.options = $.extend({
            searchProperties: [],
            searchPropertiesSelected: [],
            searchAssociations: [],
            searchAssociationsSelected: [],
            linkedPropertiesSelected: []
        }, options);
        this.init(options);
    }

    ConcreteExpressEntryListBlockForm.prototype.initEntitySelector = function() {
        var $source = $('#search'),
            $searchFieldSelectorContainer = $('div[data-container=search-field-selector]');
            $customizeContainer = $('div[data-container=customize-results]');
            _searchAttributesTemplate = _.template($('script[data-template=express-attribute-search-list]').html()),
            _searchAssociationsTemplate = _.template($('script[data-template=express-association-search-list]').html()),
            _linkedAttributesTemplate = _.template($('script[data-template=express-attribute-link-list]').html()),
            my = this;

        $source.find('select[name=exEntityID]').on('change', function() {
            var exEntityID = $(this).val();
            if (exEntityID) {
                $.concreteAjax({
                    url: $(this).attr('data-action'),
                    data: {'exEntityID': $(this).val()},
                    success: function(r) {
                        $searchFieldSelectorContainer.html(r.searchFields);
                        // activate the search fields.
                        $('div[data-component=search-field-selector]').concreteSearchFieldSelector({});
                        $customizeContainer.html(r.customize);
                        my.setSearchableProperties(r.attributes);
                        my.setSearchableAssociations(r.associations);
                        my.setLinkableProperties(r.attributes);
                    }
                });
            }
        });
    }

    ConcreteExpressEntryListBlockForm.prototype.setSearchableProperties = function(attributes, selected) {
        var $attributesContainer = $('#search div[data-container=advanced-search]');
        $attributesContainer.html(_searchAttributesTemplate({attributes: attributes, selected: selected}));
    }

    ConcreteExpressEntryListBlockForm.prototype.setSearchableAssociations = function(associations, selected) {
        var $associationsContainer = $('#search div[data-container=search-associations]');
        $associationsContainer.html(_searchAssociationsTemplate({associations: associations, selected: selected}));
    }

    ConcreteExpressEntryListBlockForm.prototype.setLinkableProperties = function(attributes, selected) {
        var $attributesContainer = $('#results div[data-container=linked-attributes]');
        $attributesContainer.html(_linkedAttributesTemplate({attributes: attributes, selected: selected}));
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

    ConcreteExpressEntryListBlockForm.prototype.init = function() {

        var my = this;
        my.initEntitySelector();
        my.initToggling();

        if (my.options.searchProperties.length) {
            my.setSearchableProperties(my.options.searchProperties, my.options.searchPropertiesSelected);
            my.setSearchableAssociations(my.options.searchAssociations, my.options.searchAssociationsSelected);
            my.setLinkableProperties(my.options.searchProperties, my.options.linkedPropertiesSelected);
            $('div[data-component=search-field-selector]').concreteSearchFieldSelector({});
        }


    }


    Concrete.event.bind('block.express_entry_list.open', function(e, data) {
        new ConcreteExpressEntryListBlockForm(data);

    });


});
