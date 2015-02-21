/**
 * concrete5 wrapper for jQuery UI
 */

$.widget.bridge( "jqdialog", $.ui.dialog );
// wrap our old dialog function in the new dialog() function.
jQuery.fn.dialog = function() {
    // Pass this over to jQuery UI Dialog in a few circumstances
    if (arguments.length > 0) {
        $(this).jqdialog(arguments[0], arguments[1], arguments[2]);
        return;
    } else if ($(this).is('div')) {
        $(this).jqdialog();
        return;
    }
    // LEGACY SUPPORT
    return $(this).each(function() {
        $(this).unbind('click.make-dialog').bind('click.make-dialog', function(e) {
            var href = $(this).attr('href');
            var width = $(this).attr('dialog-width');
            var height =$(this).attr('dialog-height');
            var title = $(this).attr('dialog-title');
            var onOpen = $(this).attr('dialog-on-open');
            var onDestroy = $(this).attr('dialog-on-destroy');
            /*
             * no longer necessary. we auto detect
                var appendButtons = $(this).attr('dialog-append-buttons');
            */
            var onClose = $(this).attr('dialog-on-close');
            var onDirectClose = $(this).attr('dialog-on-direct-close');
            obj = {
                modal: true,
                href: href,
                width: width,
                height: height,
                title: title,
                onOpen: onOpen,
                onDestroy: onDestroy,
                onClose: onClose,
                onDirectClose: onDirectClose
            }
            jQuery.fn.dialog.open(obj);
            return false;
        });
    });
}

jQuery.fn.dialog.close = function(num) {
    num++;
    $("#ccm-dialog-content" + num).jqdialog('close');
}

jQuery.fn.dialog.open = function(options) {
    if (typeof(ConcreteMenu) != 'undefined') {
        var activeMenu = ConcreteMenuManager.getActiveMenu();
        if (activeMenu) {
            activeMenu.hide();
        }
    }

    if (typeof(options.width) == 'string') {
        if (options.width == 'auto') {
            w = 'auto';
        } else {
            if (options.width.indexOf('%', 0) > 0) {
                w = options.width.replace('%', '');
                w = $(window).width() * (w / 100);
                w = w + 50;
            } else {
                w = parseInt(options.width) + 50;
            }
        }
    } else if (options.width) {
        w = parseInt(options.width) + 50;
    } else {
        w = 550;
    }

    if (typeof(options.height) == 'string') {
        if (options.height == 'auto') {
            h = 'auto';
        } else {
            if (options.height.indexOf('%', 0) > 0) {
                h = options.height.replace('%', '');
                h = $(window).height() * (h / 100);
                h = h + 100;
            } else {
                h = parseInt(options.height) + 100;
            }
        }
    } else if (options.height) {
        h = parseInt(options.height) + 100;
    } else {
        h = 400;
    }
    if (h !== 'auto' && h > $(window).height()) {
        h = $(window).height();
    }

    options.width = w;
    options.height = h;

    var defaults = {
        'modal': true,
        'escapeClose': true,
        'width': w,
        'height': h,
        'resizable': true,

        'create': function() {
            $(this).parent().addClass('animated fadeIn');
        },

        'open': function() {
            var $dialog = $(this);
            var nd = $(".ui-dialog").length;
            if (nd == 1) {
                $("body").attr('data-last-overflow', $('body').css('overflow'));
                $("body").css("overflow", "hidden");
            }
            var overlays = $('.ui-widget-overlay').length;
            $('.ui-widget-overlay').each(function(i, obj) {
                if ((i + 1) < overlays) {
                    $(this).css('opacity', 0);
                }
            });

            jQuery.fn.dialog.activateDialogContents($dialog);

            // on some brother (eg: Chrome) the resizable get hidden because the button pane 
            // in on top of it, here is a fix for this:
            if ( $dialog.jqdialog('option', 'resizable') )
            {
                var $wrapper = $($dialog.parent());
                var z = parseInt($wrapper.find('.ui-dialog-buttonpane').css('z-index'));
                $wrapper.find('.ui-resizable-handle').css('z-index', z + 1000);
            }

            if (typeof options.onOpen != "undefined") {
                if ((typeof options.onOpen) == 'function') {
                    options.onOpen($dialog);
                } else {
                    eval(options.onOpen);
                }
            }


        },
        'beforeClose': function() {
            var nd = $(".ui-dialog").length;
            if (nd == 1) {
                $("body").css("overflow", $('body').attr('data-last-overflow'));
            }
        },
        'close': function(ev, u) {
            if (!options.element) {
                $(this).jqdialog('destroy').remove();
            }
            if (typeof options.onClose != "undefined") {
                if ((typeof options.onClose) == 'function') {
                    options.onClose();
                } else {
                    eval(options.onClose);
                }
            }
            if (typeof options.onDirectClose != "undefined" && ev.handleObj && (ev.handleObj.type == 'keydown' || ev.handleObj.type == 'click')) {
                if ((typeof options.onDirectClose) == 'function') {
                    options.onDirectClose();
                } else {
                    eval(options.onDirectClose);
                }
            }
            if (typeof options.onDestroy != "undefined") {
                if ((typeof options.onDestroy) == 'function') {
                    options.onDestroy();
                } else {
                    eval(options.onDestroy);
                }
            }
            var overlays = $('.ui-widget-overlay').length;
            $('.ui-widget-overlay').each(function(i, obj) {
                if ((i + 1) < overlays) {
                    $(this).css('opacity', 0);
                } else {
                    $(this).css('opacity', 1);
                }
            });
        }
    };

    var finalSettings = {'autoOpen': false, 'data': {} };
    $.extend(finalSettings, defaults, options);

    if (finalSettings.element) {
        $(finalSettings.element).jqdialog(finalSettings).jqdialog();
        $(finalSettings.element).jqdialog('open');
    } else {
        jQuery.fn.dialog.showLoader();
        $.ajax({
            type: 'GET',
            url: finalSettings.href,
            data: finalSettings.data,
            success: function(r) {
                jQuery.fn.dialog.hideLoader();
                // note the order here is very important in order to actually run javascript in
                // the pages we load while having access to the jqdialog object.
                // Ensure that the dialog is open prior to evaluating javascript.
                $('<div />').jqdialog(finalSettings).html(r).jqdialog('open');
            }
        });
    }

}

jQuery.fn.dialog.activateDialogContents = function($dialog) {
    // handle buttons
    $dialog.find('button[data-dialog-action=cancel]').on('click', function() {
        jQuery.fn.dialog.closeTop();
    });
    $('[data-dialog-form]').concreteAjaxForm();

    $dialog.find('button[data-dialog-action=submit]').on('click', function() {
        $('[data-dialog-form]').submit();
    });

    if ($dialog.find('.dialog-buttons').length > 0) {
        $dialog.jqdialog('option', 'buttons', [{}]);
        $dialog.parent().find(".ui-dialog-buttonset").remove();
        $dialog.parent().find(".ui-dialog-buttonpane").html('');
        $dialog.find('.dialog-buttons').removeClass().appendTo($dialog.parent().find('.ui-dialog-buttonpane').addClass("ccm-ui"));
    }

    // make dialogs
    $dialog.find('.dialog-launch').dialog();

    // automated close handling
    $dialog.find('.ccm-dialog-close').on('click', function() {
        $dialog.dialog('close');
    });

    $dialog.find('.launch-tooltip').tooltip({'container': '#ccm-tooltip-holder'});

    // help handling
    if ($dialog.find('.dialog-help').length > 0) {
        $dialog.find('.dialog-help').hide();
        var helpContent = $dialog.find('.dialog-help').html();
        if (ccmi18n.helpPopup) {
            var helpText = ccmi18n.helpPopup;
        } else {
            var helpText = 'Help';
        }
        var button = $('<button class="ui-dialog-titlebar-help ccm-menu-help-trigger"><i class="fa fa-info-circle"></i></button>'),
            container = $('#ccm-tooltip-holder');
        $dialog.parent().find('.ui-dialog-titlebar').addClass('ccm-ui').append(button);
        button.popover({
            content: function() {
                return helpContent;
            },
            placement: 'bottom',
            html: true,
            container: container,
            trigger: 'click'
        });
        button.on('shown.bs.popover', function() {
            var binding = function() {
                button.popover('hide', button);
                binding = $.noop;
            };

            button.on('hide.bs.popover', function(event) {
                button.unbind(event);
                binding = $.noop;
            });

            $('body').mousedown(function(e) {
                if ($(e.target).closest(container).length || $(e.target).closest(button).length) {
                    return;
                }
                $(this).unbind(e);
                binding();
            });
        });
    }
}

jQuery.fn.dialog.getTop = function() {
    var nd = $(".ui-dialog:visible").length;
    return $($('.ui-dialog:visible')[nd-1]).find('.ui-dialog-content');
}

jQuery.fn.dialog.replaceTop = function(html) {
    $dialog = jQuery.fn.dialog.getTop();
    $dialog.html(html);
    jQuery.fn.dialog.activateDialogContents($dialog);
}

jQuery.fn.dialog.showLoader = function(text) {
    $('body').addClass('ccm-loading');
}

jQuery.fn.dialog.hideLoader = function() {
    $('body').removeClass('ccm-loading');
}


jQuery.fn.dialog.closeTop = function() {
    $dialog = jQuery.fn.dialog.getTop();
    $dialog.jqdialog('close');
}

jQuery.fn.dialog.closeAll = function() {
    $($(".ui-dialog-content").get().reverse()).jqdialog('close');
}

