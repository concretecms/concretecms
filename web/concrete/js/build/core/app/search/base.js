/**
 * Base search class for AJAX searching
 */

!function(global, $) {
    'use strict';

    function ConcreteAjaxSearch($element, options) {
        options = options || {};
        options = $.extend({
            'result': {},
            'onLoad': false,
            'onUpdateResults': false
        }, options);
        this.$element = $element;
        this.$results = $element.find('div[data-search-element=results]');
        this.$resultsTableBody = this.$results.find('tbody');
        this.$resultsTableHead = this.$results.find('thead');
        this.$resultsPagination = this.$results.find('div.ccm-search-results-pagination');
        this.$menuTemplate = $element.find('script[data-template=search-results-menu]');
        this.$searchFieldRowTemplate = $element.find('script[data-template=search-field-row]');

        this.options = options;

        this._templateSearchForm = _.template($element.find('script[data-template=search-form]').html());
        this._templateSearchResultsTableHead = _.template($element.find('script[data-template=search-results-table-head]').html());
        this._templateSearchResultsTableBody = _.template($element.find('script[data-template=search-results-table-body]').html());
        this._templateSearchResultsPagination = _.template($element.find('script[data-template=search-results-pagination]').html());

        if (this.$menuTemplate.length) {
            this._templateSearchResultsMenu = _.template(this.$menuTemplate.html());
        }
        if (this.$searchFieldRowTemplate.length) {
            this._templateAdvancedSearchFieldRow = _.template(this.$searchFieldRowTemplate.html());
        }

        this.setupSearch();
        this.setupCheckboxes();
        this.setupBulkActions();
        this.setupSort();
        this.setupPagination();
        this.setupSelect2();
        this.setupAdvancedSearch();
        this.setupCustomizeColumns();
        this.updateResults(options.result);

        if (options.onLoad) {
            options.onLoad(this);
        }
    }

    ConcreteAjaxSearch.prototype.ajaxUpdate = function(url, data, callback) {
        var cs = this;
        $.concreteAjax({
            url: url,
            data: data,
            success: function(r) {
                if (!callback) {
                    cs.updateResults(r);
                } else {
                    callback(r);
                }
            }
        })
    }

    ConcreteAjaxSearch.prototype.setupSelect2 = function() {
        var selects = this.$element.find('.select2-select');
        if (selects.length) {
            selects.select2();
        }
    }

    ConcreteAjaxSearch.prototype.createMenu = function($selector) {
        $selector.concreteMenu({
            'menu': $('[data-search-menu=' + $selector.attr('data-launch-search-menu') + ']')
        });
    }

    ConcreteAjaxSearch.prototype.setupMenus = function(result) {
        var cs = this;
        if (cs._templateSearchResultsMenu) {
            cs.$element.find('[data-search-menu]').remove();

            // loop through all results,
            // create nodes for them.
            $.each(result.items, function(i, item) {
                cs.$results.append(cs._templateSearchResultsMenu({'item': item}));
            });

            cs.$results.find(".ccm-popover-file-menu li a").on( 'click', function( evt ) {
                var $anchor = $(this);
                var type    = $(this).attr('data-filemenu-type');

                var container = $(this).closest('div[data-search-file-menu]');
                var item = container.attr('data-search-file-menu');

                ConcreteAjaxSearch.handleFileMenuAction(type, $anchor, { fID: item } );
                return false;
            });

            cs.$element.find('tbody tr').each(function() {
                cs.createMenu($(this));
            });
        }
    }

    ConcreteAjaxSearch.prototype.setupCustomizeColumns = function() {
        var cs = this;
        cs.$element.on('click', 'a[data-search-toggle=customize]', function() {
            var url = $(this).attr('data-search-column-customize-url');
            $.fn.dialog.open({
                width: 480,
                height: 400,
                href: url,
                modal: true,
                title: ccmi18n.customizeSearch,
                onOpen: function() {
                    var $form = $('form[data-dialog-form=search-customize]'),
                        $selectDefault = $form.find('select[data-search-select-default-column]'),
                        $columns = $form.find('ul[data-search-column-list]');

                    $('ul[data-search-column-list]').sortable({
                        cursor: 'move',
                        opacity: 0.5
                    });
                    $form.on('click', 'input[type=checkbox]', function() {
                        var label = $(this).parent().find('span').html(),
                            id = $(this).attr('id');

                        if ($(this).prop('checked')) {
                            if ($form.find('li[data-field-order-column=\'' + id + '\']').length == 0) {
                                $selectDefault.append($('<option>', {'value': id, 'text': label}));
                                $selectDefault.prop('disabled', false);
                                $columns.append('<li data-field-order-column="' + id + '"><input type="hidden" name="column[]" value="' + id + '" />' + label + '<\/li>');
                            }
                        } else {
                            $columns.find('li[data-field-order-column=\'' + id + '\']').remove();
                            $selectDefault.find('option[value=\'' + id + '\']').remove();
                            if ($columns.find('li').length == 0) {
                                $selectDefault.prop('disabled', true);
                            }
                        }
                    });
                    ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function(e, data) {
                        cs.updateResults(data.response.result);
                    });
                }
            });
            return false;
        });
    }

    ConcreteAjaxSearch.prototype.updateResults = function(result) {
        var cs = this,
            options = cs.options;
        cs.$resultsTableHead.html(cs._templateSearchResultsTableHead({'columns': result.columns}));
        cs.$resultsTableBody.html(cs._templateSearchResultsTableBody({'items': result.items}));
        cs.$resultsPagination.html(cs._templateSearchResultsPagination({'paginationTemplate': result.paginationTemplate}));
        cs.$advancedFields.html('');
        $.each(result.fields, function(i, field) {
            cs.$advancedFields.append(cs._templateAdvancedSearchFieldRow({'field': field}));
        });
        cs.setupMenus(result);
        if (options.onUpdateResults) {
            options.onUpdateResults(this);
        }
    }

    ConcreteAjaxSearch.prototype.setupAdvancedSearch = function() {
        var cs = this;
        cs.$advancedFields = cs.$element.find('div.ccm-search-fields-advanced');

        cs.$element.on('click', 'a[data-search-toggle=advanced]', function() {
            cs.$advancedFields.append(cs._templateAdvancedSearchFieldRow());
            return false;
        });
        cs.$element.on('change', 'select[data-search-field]', function() {
            var $content = $(this).parent().find('.ccm-search-field-content');
            $content.html('');
            var field = $(this).find(':selected').attr('data-search-field-url');
            if (field) {
                cs.ajaxUpdate(field, false, function(r) {
                    $content.html(r.html);
                });
            }
        });
        cs.$element.on('click', 'a[data-search-remove=search-field]', function() {
            var $row = $(this).parent();
            $row.remove();
            return false;
        });
    }

    ConcreteAjaxSearch.prototype.setupSort = function() {
        var cs = this;
        this.$element.on('click', 'thead th a', function() {
            cs.ajaxUpdate($(this).attr('href'));
            return false;
        });
    }

    ConcreteAjaxSearch.prototype.refreshResults = function() {
        var cs = this;
        cs.$element.find('form[data-search-form]').trigger('submit');
    }

    ConcreteAjaxSearch.prototype.setupSearch = function() {
        var cs = this;
        cs.$element.find('[data-search-element=wrapper]').html(cs._templateSearchForm());
        cs.$element.on('submit', 'form[data-search-form]', function() {
            var data = $(this).serializeArray();
            data.push({'name': 'submitSearch', 'value': '1'});
            cs.ajaxUpdate($(this).attr('action'), data);
            return false;
        });
    }


    ConcreteAjaxSearch.prototype.publish = function(eventName, data) {
        var cs = this;
        ConcreteEvent.publish(eventName, data, cs);
    }

    ConcreteAjaxSearch.prototype.subscribe = function(eventName, callback) {
        var cs = this;
        ConcreteEvent.subscribe(eventName, callback, cs);
    }
    ConcreteAjaxSearch.prototype.setupBulkActions = function() {
        var cs = this;

        cs.$element.find('.dropdown-toggle').dropdown();

        cs.$bulkActionsMenu = cs.$element.find( "div.ccm-search-bulk-action" );


        cs.$targetDropDn   = cs.$bulkActionsMenu.find( "button.dropdown-toggle");
        cs.$targetButtons  = cs.$bulkActionsMenu.find( '.ccm-action-target-control .btn-group' );

        cs.$targetSelected = cs.$targetButtons.find( 'button.ccm-target-selected' );
        cs.$targetUploaded = cs.$targetButtons.find( 'button.ccm-target-uploaded' );

        cs.updateTargetButtons = function () {
            var $btn = $(this);
            var found = false;

            var sel = cs.getSelectedItems();

            if ( sel.length == 0 ) {
                cs.$targetSelected.enable(false);
                cs.$targetSelected.addClass('disabled');
            } else{
                found = true;
                cs.$targetSelected.enable(true);
                cs.$targetSelected.removeClass('disabled');
            }

            var up = cs.getUploadedItems();

            if ( up.length == 0 ) {
                cs.$targetUploaded.enable(false);
                cs.$targetUploaded.addClass("disabled");
            } else {
                found = true;
                cs.$targetUploaded.enable(true);
                cs.$targetUploaded.removeClass("disabled");
            }

            if ( !found ) {
                cs.$targetButtons.find("button").removeClass("active");
                cs.$targetDropDn.addClass("disabled");
            }
            else cs.$targetDropDn.removeClass("disabled");
 
            if ($btn ) {
                // called from a button
                cs.$targetButtons.find("button").removeClass("active");
                $btn.addClass("active");
                event.stopPropagation()
            }
        }

        $(function(){cs.updateTargetButtons()});

        cs.$targetButtons.find("button").on('click', cs.updateTargetButtons );

        cs.getUploadedItems = function() {
            var itemIDs = [];
            var uploaded = $('#ccm-search-uploaded-fIDs').val().trim();
            if ( "" != uploaded ) itemIDs = uploaded.split(",");
            if ( itemIDs[itemIDs.length-1] == '' ) itemIDs.pop();
            return itemIDs;
        }

        cs.getSelectedItems = function() {
            var itemIDs = [];
            var $items = cs.$element.find('input[data-search-checkbox=individual]:checked');

            $.each($items, function(i, checkbox) {
                itemIDs.push($(checkbox).val());
            });
            return itemIDs;
        }


        cs.getTargetItems = function() {
            if (cs.$targetSelected.hasClass('active') ) return cs.getSelectedItems();
            else  return cs.getUploadedItems();
        }

        cs.$bulkActionsMenu.find("li a").on( 'click', function( evt ) {
            var $anchor = $(this);
            var type    = $(this).attr('data-filemenu-type');

            var items = cs.getTargetItems();

            if (items.length == 0 ) return false;

            cs.handleSelectedBulkAction(type, $anchor, cs.getTargetItems() );
            return false;
        });


        cs.$bulkActions = cs.$element.find('select[data-bulk-action]');

    }

    ConcreteAjaxSearch.prototype.setupPagination = function() {
        var cs = this;
        this.$element.on('click', 'ul.pagination a', function() {
            cs.ajaxUpdate($(this).attr('href'));
            return false;
        });
    }

    ConcreteAjaxSearch.prototype.setupCheckboxes = function() {
        var cs = this;
        cs.$element.on('click', 'input[data-search-checkbox=select-all]', function() {
            cs.$element.find('input[data-search-checkbox=individual]').prop('checked', $(this).is(':checked')).trigger('change');
        });
        cs.$element.on('change', 'input[data-search-checkbox=individual]', function() {
            cs.updateTargetButtons();
            cs.$targetSelected.click();
        });

    }

    /*
     * Static Method:
     */
    ConcreteAjaxSearch.handleFileMenuAction = function(type, $anchor, params ) {

        var FileMenuItem = Concrete.const.Core.Application.UserInterface.Menu.Item.FileMenuItem;
        var target = $anchor.attr('href') + '?' + jQuery.param(params);

        if ( type == FileMenuItem.ACTION_OPEN ) {

            window.location = target;

        } else if ( type == FileMenuItem.ACTION_DOWNLOAD ) { 

            var downloadTarget = $('#ccm-file-manager-download-target');

            if ( !downloadTarget.length ) {
                downloadTarget = $('<iframe />', {
                    'name': 'ccm-file-manager-download-target',
                    'id': 'ccm-file-manager-download-target'
                }).appendTo(document.body);
            }
            downloadTarget.get(0).src = target;

        } else if ( type == FileMenuItem.ACTION_OPEN_DIALOG ) {

            var modal = true;
            var title = $anchor.find('span').text();

            if ( 'undefined' != typeof($anchor.attr('data-filemenu-title')) ) title = $anchor.attr('data-filemenu-title');
            if ( 'undefined' != typeof($anchor.attr('data-filemenu-modal')) ) modal = $anchor.attr('data-filemenu-modal') == true;

            $('iframe#ccm-file-manager-download-target').remove(); // Remove the target to prevent dialog to get appended to it
            
            jQuery.fn.dialog.open.call( $("body"), {
                width:  $anchor.attr('data-filemenu-width'),
                height: $anchor.attr('data-filemenu-height'),
                modal:  modal,
                href:   $anchor.attr('href') + '?' + jQuery.param( params ),
                title:  title,
            });

        } else if (type == FileMenuItem.ACTION_AJAX_REQUEST ) {
            $.concreteAjax({
                url: $anchor.attr('href'),
                data: params,
                success: function(r) {
                    if (r.message) {
                        ConcreteAlert.notify({
                            'message': r.message,
                            'title': r.title
                        });
                    }
                }
            });
        } 

    }

    // jQuery Plugin
    $.fn.concreteAjaxSearch = function(options) {
        return new ConcreteAjaxSearch(this, options);
    }

    global.ConcreteAjaxSearch = ConcreteAjaxSearch;

}(this, $);
