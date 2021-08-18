(function(global, $) {
    // Turns the Top Navigation Bar – which is based on the Bootstrap Navbar

    document.addEventListener("DOMContentLoaded", function(){

        // make it as accordion for smaller screens
        if (window.innerWidth > 992) {

            document.querySelectorAll('.navbar .nav-item').forEach(function(everyitem){

                everyitem.addEventListener('mouseover', function(e){

                    let el_link = this.querySelector('a[data-concrete-toggle]');

                    if(el_link != null){
                        let nextEl = el_link.nextElementSibling;
                        el_link.classList.add('show');
                        nextEl.classList.add('show');
                    }

                });
                everyitem.addEventListener('mouseleave', function(e){
                    let el_link = this.querySelector('a[data-concrete-toggle]');

                    if(el_link != null){
                        let nextEl = el_link.nextElementSibling;
                        el_link.classList.remove('show');
                        nextEl.classList.remove('show');
                    }


                })
            });

        }
        // end if innerWidth
    });

})(window, $)
