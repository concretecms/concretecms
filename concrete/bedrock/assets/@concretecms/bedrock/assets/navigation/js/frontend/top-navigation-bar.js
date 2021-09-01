(function(global, $) {

    document.addEventListener("DOMContentLoaded", function(){

        // Enable dropdown menu in navbar

        if (window.innerWidth > 992) {

            document.querySelectorAll('.ccm-block-top-navigation-bar .nav-item').forEach(function(everyitem){

                everyitem.addEventListener('mouseover', function(e){

                    let el_link = this.querySelector('a[data-bs-toggle]');

                    if(el_link != null){
                        let nextEl = el_link.nextElementSibling;
                        el_link.classList.add('show');
                        nextEl.classList.add('show');
                    }

                });
                everyitem.addEventListener('mouseleave', function(e){
                    let el_link = this.querySelector('a[data-bs-toggle]');

                    if(el_link != null){
                        let nextEl = el_link.nextElementSibling;
                        el_link.classList.remove('show');
                        nextEl.classList.remove('show');
                    }


                })
            });

        }

        // Enable transparency
        const $transparentNavbar = $('div[data-transparency=navbar]')
        const $toolbar = $('#ccm-toolbar')
        if ($transparentNavbar.length) {
            const $navbar = $transparentNavbar.find('.navbar')
            // Check the next item to see if it supports transparency

            if ($navbar.hasClass('fixed-top') && $toolbar.length > 0) {
                $navbar.removeClass('fixed-top')
            }

            const $nextElement = $transparentNavbar.next()
            if ($nextElement.length && $nextElement.is('[data-transparency=element]') && $toolbar.length === 0) {
                $transparentNavbar.addClass('transparency-enabled')

                if ($navbar.hasClass('fixed-top')) {
                    $(window).scroll(function() {
                        var isScrolled = $(document).scrollTop() > 5;
                        if (isScrolled) {
                            $transparentNavbar.removeClass('transparency-enabled')
                        } else {
                            $transparentNavbar.addClass('transparency-enabled')
                        }
                    })
                }
            }
            $transparentNavbar.show()
        }
    });

})(window, $)
