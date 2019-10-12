(function ($) {
    "use strict";

    var forms = $('div.controls').find('div.authentication-type').hide(),
        select = $('div.ccm-authentication-type-select > select');
    var types = $('ul.auth-types > li').each(function () {
        var me = $(this),
            form = forms.filter('[data-handle="' + me.data('handle') + '"]');
        me.click(function () {
            select.val(me.data('handle'));
            if (typeof Concrete !== 'undefined') {
                Concrete.event.fire('AuthenticationTypeSelected', me.data('handle'));
            }

            if (form.hasClass('active')) return;
            types.removeClass('active');
            me.addClass('active');
            if (forms.filter('.active').length) {
                forms.stop().filter('.active').removeClass('active').fadeOut(250, function () {
                    form.addClass('active').fadeIn(250);
                });
            } else {
                form.addClass('active').show();
            }
        });
    });

    select.change(function() {
        types.filter('[data-handle="' + $(this).val() + '"]').click();
    });
    types.first().click();

    $('ul.nav.nav-tabs > li > a').on('click', function () {
        var me = $(this);
        if (me.parent().hasClass('active')) return false;
        $('ul.nav.nav-tabs > li.active').removeClass('active');
        var at = me.attr('data-authType');
        me.parent().addClass('active');
        $('div.authTypes > div').hide().filter('[data-authType="' + at + '"]').show();
        return false;
    });

})(jQuery);
