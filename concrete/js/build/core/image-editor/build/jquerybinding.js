    // End the ImageEditor object.

    im.setActiveElement(im.stage);

    global.c5_image_editor = im; // Safe keeping
    global.im = im;
    return im;
};

$.fn.ImageEditor = function (settings) {
    if (settings === undefined) settings = {};
    settings.imageload = $.fn.dialog.hideLoader;
    var self = $(this);
    settings.container = self[0];
    if (self.height() == 0) {
        setTimeout(function () {
            self.ImageEditor(settings);
        }, 50);
        return;
    }
    self.closest('.ui-dialog').find('.ui-resizable-handle').hide();
    self.height("-=30");
    $('div.editorcontrols').height(self.height() - 90);
    self.width("-=330").parent().width("-=330").children('div.bottomBar').width("-=330");
    if(settings.width === undefined) settings.width = self.width();
    if(settings.height === undefined) settings.height = self.height();
    $.fn.dialog.showLoader();
    var im = new ImageEditor(settings);

    var context = im.domContext;
    $('div.control-sets > div.controlset', context).each(function () {
        var container = $(this),
            type = container.data('namespace');

        container.find('h4').click(function () {
            if (!container.hasClass('active')) {
                im.fire('ChangeActiveAction', 'ControlSet_' + type);
            }
        });

        im.bind('ChangeActiveAction', function(e, data) {
            if (data === 'ControlSet_' + type) {
                context.find('div.controlset.active').removeClass('active').children('.control').slideUp(250);
                container.addClass('active');

                var control = container.children('.control').height('auto');
                control.slideDown(250);
            }
        });
    });

    $('div.controls > div.controlscontainer', context).children('div.save').children('button.save').click(function () {
        $(this).attr('disabled', true);
        im.save();
    }).end().children('button.cancel').click(function () {
        if (window.confirm(ccmi18n_imageeditor.areYouSure))
            $.fn.dialog.closeTop();
    });

    im.on('ChangeActiveAction', function (e, data) {
        if (!data) {
            $('h4.active', context).removeClass('active');
        }
    });

    im.on('ChangeActiveComponent', function (e, data) {
        if (!data) {
            $('div.controlset.active', context).removeClass('active');
        }
    });

    im.bind('imageload', $.fn.dialog.hideLoader);
    return im;
};
$.fn.slideOut = function (time, callback) {
    var me = $(this),
        startWidth = me.width(),
        totalWidth = 255;
    me.css('overflow-y', 'auto');
    if (startWidth == totalWidth) {
        me.animate({width: totalWidth}, 0, callback);
        return this;
    }
    me.width(startWidth).animate({width: totalWidth}, time || 300, callback || function () {
    });
    return this;
};
$.fn.slideIn = function (time, callback) {
    var me = $(this);
    me.css('overflow-y', 'hidden');
    if (me.width() === 0) {
        me.animate({width: 0}, 0, callback);
        return this;
    }

    me.animate({width: 0}, time || 300, callback || function () {
    });
    return this;
};
