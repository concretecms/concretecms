(function(global, $) {
    var originalNav = $('.ccm-responsive-navigation');
    if(!($('.ccm-responsive-overlay').length)) {
        $('body').append('<div class="ccm-responsive-overlay"></div>');
    }
    var clonedNavigation = originalNav.clone();
    $(clonedNavigation).removeClass('original');
    $('.ccm-responsive-overlay').append(clonedNavigation);
    $('.ccm-responsive-menu-launch').click(function(){
        $('.ccm-responsive-menu-launch').toggleClass('responsive-button-close');   // slide out mobile nav
        $('.ccm-responsive-overlay').slideToggle();
    });
    $('.ccm-responsive-overlay ul li').children('ul').hide();
    $('.ccm-responsive-overlay li').each(function(index) {
        if($(this).children('ul').size() > 0) {
            $(this).addClass('parent-ul');
        } else {
            $(this).addClass('last-li');
        }
    });
    $('.ccm-responsive-overlay .parent-ul a').click(function(event) {
        if(!($(this).parent('li').hasClass('last-li'))) {
            $(this).parent('li').siblings().children('ul').hide();
            if($(this).parent('li').children('ul').is(':visible')) {
            } else {
                $(this).next('ul').show();
                event.preventDefault();
            }
        }
    });
})(window, $);