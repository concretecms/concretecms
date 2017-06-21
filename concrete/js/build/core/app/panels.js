/**
 * Left and right panels
 */
var html = $('html');
function ConcretePanel(options) {
    'use strict';
    this.options = options;
    this.isOpen = false;
    this.detail = false;
    this.isPinned = false;

    this.pinned = function() { return this.isPinned; }

    this.willBePinned = function() { return this.isOpen && !this.isPinned; }

    this.isPinable = function() { return this.options.pinable; }

    this.getPositionClass = function () {
        var ccm_class;
        switch (options.position) {
            case 'left':
                ccm_class = 'ccm-panel-left';
                break;
            case 'right':
                ccm_class = 'ccm-panel-right';
                break;
        }

        switch (options.transition) {
            case 'slide':
                ccm_class += ' ccm-panel-transition-slide';
                break;
            default:
                ccm_class += ' ccm-panel-transition-none';
                break;
        }
        return ccm_class;
    };

    this.getURL = function () {
        return this.options.url;
    };

    this.getCurrentURL = function () {
        return this.options.currentUrl;
    };

    this.getIdentifier = function () {
        return this.options.identifier;
    };

    this.getDOMID = function () {
        return 'ccm-panel-' + this.options.identifier.replace('/', '-');
    };

    this.onPanelLoad = function (element) {
        var $link = $('a[data-launch-panel=' + this.getIdentifier() + ']');
        var hasTooltip = $link.hasClass('launch-tooltip');

        this.setupSubPanels();
        this.setupPanelDetails();
        $link.removeClass().addClass('ccm-launch-panel-active');
        if (hasTooltip) {
            $link.addClass('launch-tooltip');
        }
        Concrete.event.publish('PanelLoad', {panel: this, element: element});
    };

    this.hide = function (callback) {
        callback = callback || $.noop;
        var me = this;
        this.closePanelDetail(function () {
            var obj = this;
            var $link = $('a[data-launch-panel=' + this.getIdentifier() + ']');
            $link.removeClass();
            $('#' + obj.getDOMID()).removeClass('ccm-panel-active');
            $('#ccm-panel-overlay').queue(function () {
                $(this).removeClass('ccm-panel-translucent');
                $(this).dequeue();
            }).delay(1000).hide(0);
            html.removeClass(obj.getPositionClass());
            html.removeClass('ccm-panel-open');
            obj.isOpen = false;
            $(this).dequeue();

            callback.call(me);
            setTimeout(function() {
                Concrete.event.publish('PanelClose', {panel: obj});
            }, 0);
        });
    };

    this.toggle = function () {
        if (this.isOpen) {
            if (this.options.pinable )
            {
                if (!this.isPinned )
                {
                    this.isPinned = true;
                }
                else
                {
                    this.isPinned = false;
                    this.hide();
                }
            } else this.hide();
        } else {
            this.show();
        }
    };

    this.setupSubPanels = function () {
        var $panel = $('#' + this.getDOMID());
        var obj = this;
        $panel.find('[data-launch-sub-panel-url]').unbind('.sub').on('click.sub', function () {
            var cover = $('<div />').addClass('ccm-panel-cover').appendTo($(this).closest('.ccm-panel'));
            obj.closePanelDetailImmediately();
            var url = $(this).attr('data-launch-sub-panel-url');
            $('<div />', {'class': 'ccm-panel-content ccm-panel-content-appearing'}).appendTo($panel.find('.ccm-panel-content-wrapper')).load(url + '?cID=' + CCM_CID, function () {
                _.delay(function() {
                    cover.remove();
                }, 250);
                $panel.find('.ccm-panel-content-visible').removeClass('ccm-panel-content-visible').addClass('ccm-panel-slide-left');
                $(this).removeClass('ccm-panel-content-appearing').addClass('ccm-panel-content-visible');

                obj.options.currentUrl = url;
                obj.onPanelLoad(this);
            });
            $(this).removeClass('ccm-panel-menu-item-active');
            return false;
        });
        $panel.find('[data-panel-navigation=back]').unbind().on('click.navigate', function () {
            obj.goBack();
            return false;
        });
    };

    this.goBack = function() {
        var $panel = $('#' + this.getDOMID());
        this.closePanelDetailImmediately();

        $panel
            .queue(function () {
                var $prev = $panel.find('.ccm-panel-content-visible').prev();
                $panel.find('.ccm-panel-content-visible').removeClass('ccm-panel-content-visible').addClass('ccm-panel-slide-right');
                $prev.removeClass('ccm-panel-slide-left').addClass('ccm-panel-content-visible');
                $panel.dequeue();
            })
            .delay(500)
            .queue(function () {
                $panel.find('.ccm-panel-slide-right').remove();
                $panel.dequeue();
            });
    };

    this.showPanelConfirmationMessage = function (id, msg, buttons) {
        var html = '<div id="ccm-panel-confirmation-' + id + '" class="ccm-ui ccm-panel-confirmation-wrapper"><div class="ccm-panel-confirmation">';
        html += '<p>' + msg + '</p><div class="ccm-panel-confirmation-buttons"></div>';
        html += '</div></div>';
        $(html).appendTo(document.body);
        var $dialog = $('#ccm-panel-confirmation-' + id);
        $dialog.delay(0).queue(function () {
            $(this).addClass('ccm-panel-confirmation-displayed');
            $(this).dequeue();
        });

        var myButtons = [
            {'class': 'btn pull-left btn-sm btn-link', 'type': 'button', 'data-panel-confirmation-action': 'cancel', 'text': ccmi18n.cancel}
        ];
        for (var i = 0; i < buttons.length; i++) {
            myButtons.push(buttons[i]);
        }
        $.each(myButtons, function (i, button) {
            $dialog.find('.ccm-panel-confirmation-buttons').append($('<button />', button));
        });
        $dialog.find('button').on('click', function () {
            $dialog.delay(0).queue(function () {
                $dialog.removeClass('ccm-panel-confirmation-displayed');
                $dialog.addClass('ccm-panel-confirmation-disappearing');
                $(this).dequeue();
            }).delay(350).queue(function () {
                $dialog.remove();
            });
        });
    };

    this.closePanelDetail = function (callback) {
        callback = callback || $.noop;
        var me = this;
        if (!this.detail) {
            callback.call(me);
            return false;
        }

        $('a[data-launch-panel-detail=' + this.detail.identifier + ']').removeClass('ccm-panel-menu-item-active');
        var transition = this.detail.transition;
        $('div.ccm-panel-detail').queue(function () {
            $(this).removeClass('ccm-panel-detail-transition-' + transition + '-apply');
            $(this).dequeue();
        }).delay(550).queue(function () {
            $(this).remove();
            $(this).dequeue();
        });


        $('div.ccm-page').queue(function () {
            $(this).removeClass('ccm-panel-detail-transition-' + transition + '-apply');
            $(this).dequeue();
        }).delay(550).queue(function () {
            html.removeClass('ccm-panel-detail-open');
            $(this).addClass('ccm-panel-detail-disable-transition');
            $(this).dequeue();
        }).delay(1).queue(function () {
            $(this).removeClass('ccm-panel-detail-transition-' + transition);
            $(this).dequeue();
        }).delay(1).queue(function () {
            $(this).removeClass('ccm-panel-detail-disable-transition');
            $(this).dequeue();

            callback.call(me);
        });

        $('#ccm-panel-detail-form-actions-wrapper .ccm-panel-detail-form-actions').queue(function () {
            $(this).css('opacity', 0);
            $(this).dequeue(0);
        }).delay(550).queue(function () {
            $(this).remove();
            $(this).dequeue();
        });

        Concrete.event.publish('PanelCloseDetail', this.detail);
        this.detail = false;

        if ($('.ccm-panel-detail').length > 0) {
            return 550;
        }

    };

    this.closePanelDetailImmediately = function () {
        if (!this.detail) {
            return false;
        }
        html.removeClass('ccm-panel-detail-open');
        $('.ccm-panel-detail').remove();
        $('.ccm-panel-detail-form-actions').remove();
        $('.ccm-page').removeClass().addClass('ccm-page');
        Concrete.event.publish('PanelCloseDetail', this.detail);
        this.detail = false;
    };

    this.openPanelDetail = function (overrides) {
        var obj = this;
        var options = $.extend({
            transition: 'none',
            url: false,
            data: ''
        }, overrides);
        var identifier = options.identifier;
        // if a panel is already open, we close it immediately
        if (obj.detail) {
            obj.closePanelDetailImmediately();
        }
        obj.detail = options;

        var detailID = 'ccm-panel-detail-' + identifier;

        var $detail = $('<div />', {
            id: detailID,
            class: 'ccm-panel-detail'
        }).appendTo(document.body);

        var $content = $('<div />', {
            class: 'ccm-ui ccm-panel-detail-content'
        }).appendTo($detail);

        $('div.ccm-page')
            .queue(function () {
                $detail.addClass('ccm-panel-detail-transition-' + options.transition);
                $(this).addClass('ccm-panel-detail-transition-' + options.transition);
                $(this).dequeue();
            })
            .delay(3)
            .queue(function () {
                $detail.addClass('ccm-panel-detail-transition-' + options.transition + '-apply');
                $(this).addClass('ccm-panel-detail-transition-' + options.transition + '-apply');
                $(this).dequeue();
            });
        html.addClass('ccm-panel-detail-open');

        var complete_function = function () {
            Concrete.event.publish('PanelOpenDetail', {
                panel: options,
                panelObj: obj,
                container: $content
            });
        };

        if (options.url) {
            var url = options.url + '?cID=' + CCM_CID, data = null;
            if ($.isPlainObject(options.data)) {
                data = options.data;
            } else {
                url += options.data;
            }
            $content.load(url, data, function () {
                jQuery.fn.dialog.hideLoader();
                $content.find('.launch-tooltip').tooltip({'container': '#ccm-tooltip-holder'});
                $content.find('a[data-help-notification-toggle]').concreteHelpLauncher();
                obj.loadPanelDetailActions($content);

                _.defer(complete_function);
            });
        } else {
            jQuery.fn.dialog.hideLoader();
            $content.find('.launch-tooltip').tooltip({'container': '#ccm-tooltip-holder'});
            $content.find('a[data-help-notification-toggle]').concreteHelpLauncher();
            obj.loadPanelDetailActions($content);

            _.defer(complete_function);
        }
    };

    this.loadPanelDetailActions = function ($content) {
        var obj = this;
        var $actions = $content.find('.ccm-panel-detail-form-actions');
        if ($actions.length) {
            $(document.body).delay(500)
                .queue(function () {
                    var $wrapper = $('#ccm-panel-detail-form-actions-wrapper');
                    if (!$wrapper.length) {
                        $wrapper = $('<div />', {
                            id: 'ccm-panel-detail-form-actions-wrapper',
                            class: 'ccm-ui'
                        });
                        $wrapper.appendTo(document.body);
                    }
                    $wrapper.html('').append($actions);
                    $(this).dequeue();
                })
                .delay(5)
                .queue(function () {
                    $('#ccm-panel-detail-form-actions-wrapper .ccm-panel-detail-form-actions').css('opacity', 1);
                    $(this).dequeue();
                });
            $('button[data-panel-detail-action=cancel]').on('click', function () {
                obj.closePanelDetail();
            });

            $content.find('[data-panel-detail-form]').concreteAjaxForm();

            $('button[data-panel-detail-action=submit]').on('click', function () {
                $('[data-panel-detail-form]').submit();
            });

            ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function(e, data) {
                if ($('[data-panel-detail-form="'+ data.form + '"]').data('action-after-save')=='reload') {
                    location.reload();
                }
            });
        }
    };

    this.setupPanelDetails = function () {
        var $panel = $('#' + this.getDOMID());
        var obj = this;
        $panel.find('.launch-tooltip').tooltip({'container': '#ccm-tooltip-holder'});
        $panel.find('[data-panel-menu=accordion]').each(function () {
            var $accordion = $(this);
            var $title = $(this).find('>nav>span');
            $title.text($(this).find('a[data-panel-accordion-tab-selected=true]').text());
            $title.unbind('.accordion').on('click.accordion', function () {
                $accordion.toggleClass('ccm-panel-header-accordion-dropdown-visible');
            });
            $(this).find('>nav ul a').unbind('.accordion').on('click.accordion', function () {
                var url = obj.getCurrentURL();
                if (!url) {
                    url = obj.getURL();
                }
                var $content = $panel.find('.ccm-panel-content');
                $accordion.removeClass('ccm-panel-header-accordion-dropdown-visible');
                $title.html($(this).text());
                $content.load(url + '?cID=' + CCM_CID + '&tab=' + $(this).attr('data-panel-accordion-tab'), function () {
                    obj.onPanelLoad(this);
                });
            });
        });
        $panel.find('.dialog-launch').dialog();
        $panel.find('[data-panel-menu=collapsible-list-group]').each(function () {
            var $clg = $(this);
            var $inner = $clg.find('.list-group-item-collapse-wrapper');
            var menuID = $clg.attr('data-panel-menu-id');
            var $title = $clg.find('.list-group-item-collapse span');
            var height;

            $clg.find('.list-group-item-collapse').unbind('.clg').on('click.clg', function () {

                if ($clg.hasClass('ccm-panel-list-group-item-expanded')) {
                    $title.text(ccmi18n.expand);
                    Concrete.event.publish('PanelCollapsibleListGroupCollapse', menuID);
                    $inner.height(0);
                } else {
                    height = $inner.show().height('').outerHeight();
                    $inner.height(0);
                    Concrete.event.publish('PanelCollapsibleListGroupExpand', menuID);
                    $title.text(ccmi18n.collapse);
                    $inner.height(height);
                }
                $clg.toggleClass('ccm-panel-list-group-item-expanded');
            });
        });
        $panel.find('[data-launch-panel-detail]').unbind('.detail').on('click.detail', function () {
            jQuery.fn.dialog.showLoader();
            $('.ccm-panel-menu-item-active').removeClass('ccm-panel-menu-item-active');
            $(this).addClass('ccm-panel-menu-item-active');
            var identifier = $(this).attr('data-launch-panel-detail');
            var panelDetailOptions = {'identifier': identifier, target: $(this)};
            if ($(this).attr('data-panel-transition')) {
                panelDetailOptions.transition = $(this).attr('data-panel-transition');
            }
            if ($(this).attr('data-panel-detail-url')) {
                panelDetailOptions.url = $(this).data('panel-detail-url');
            }
            obj.openPanelDetail(panelDetailOptions);
            return false;
        });
        obj.loadPanelDetailActions($panel);

    };

    this.show = function(callback) {
        callback = callback || $.noop;
        var element = $('#' + this.getDOMID()),
            obj = this,
            show = function() {
                html.addClass('ccm-panel-open');
                element.find('.ccm-panel-content-wrapper').html('');
                element.addClass('ccm-panel-active ccm-panel-loading');
                $('<div/>').addClass('ccm-panel-content ccm-panel-content-visible')
                    .appendTo(element.find('.ccm-panel-content-wrapper'))
                    .load(obj.getURL() + '?cID=' + CCM_CID, function() {
                        var elem = this;
                        element.delay(1).queue(function () {
                            $(this).removeClass('ccm-panel-loading').addClass('ccm-panel-loaded');
                            $(this).dequeue();
                        });
                        obj.onPanelLoad(element);
                        obj.isOpen = true;
                        Concrete.event.publish('PanelOpen', {
                            panel: obj,
                            element: elem
                        });
                        if (obj.options.overlay) {
                            ConcretePanelManager.showOverlay(obj.options.translucent);
                        }
                    });
                html.addClass(obj.getPositionClass());
            };

        if (this.options.primary) {
            var open_panel = _(ConcretePanelManager.getPanels()).findWhere({
                isOpen: true
            });
            if (open_panel) {
                open_panel.hide(function() {
                    show.call(this);
                });
            } else {
                show.call(this);
            }
        } else {
            show.call(this);
        }

        // hide mobile menu
        $('.ccm-toolbar-mobile-menu-button').removeClass('ccm-mobile-close');
        $('.ccm-mobile-menu-overlay').slideUp();
    }

}

var ConcretePanelManager = (function ConcretePanelManagerGenerator() {
    'use strict';

    var panels = [];

    return {

        getPanels: function () {
            return panels;
        },

        showOverlay: function (translucent) {
            $('#ccm-panel-overlay')
                .clearQueue()
                .show(0)
                .delay(100)
                .queue(function () {
                    if (translucent) {
                        $(this).addClass('ccm-panel-translucent');
                    } else {
                        $(this).removeClass('ccm-panel-translucent');
                    }
                    $(this).dequeue();
                });
        },

        /**
         * Hides all panels, exit preview mode, hides detail content if active, etc..
         */
        exitPanelMode: function (callback) {
            callback = callback || $.noop;
            var active = 0;
            for (var i = 0; i < panels.length; i++) {
                if (panels[i].isOpen) {
                    active++;
                    panels[i].hide(function() {
                        active--;
                        if (active == 0) {
                            callback.call(null);
                        }
                    });
                }
            }
        },

        register: function (overrides) {
            var options = $.extend({
                translucent: true,
                overlay: true,
                position: 'left',
                primary: true,
                transition: 'slide'
            }, overrides);

            var panel = new ConcretePanel(options);
            panels.push(panel);

            var $panel = $('#' + panel.getDOMID());
            if (!($panel.length)) {
                $('<div />', {
                    'id': panel.getDOMID(),
                    'class': 'ccm-panel ' + panel.getPositionClass()
                }).appendTo($(document.body));

                $('<div />', {
                    'class': 'ccm-panel-content-wrapper ccm-ui'
                }).appendTo($('#' + panel.getDOMID()));
            }
        },

        getByIdentifier: function (panelID) {
            for (var i = 0; i < panels.length; i++) {
                if (panels[i].getIdentifier() == panelID) {
                    return panels[i];
                }
            }
        }

    };
}());
