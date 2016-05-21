(function () {
    "use strict";
 
    window.C5GMaps = {
        
        pacTimer: null,
        
        init: function () {
            if (!window.C5GMaps.isMapsPresent()) {
                $('head').append($(unescape("%3Cscript src='https://maps.googleapis.com/maps/api/js?libraries=places&callback=window.C5GMaps.setupAutocomplete' type='text/javascript'%3E%3C/script%3E")));
            } else {
                window.C5GMaps.setupAutocomplete();
            }
        },
        
        isMapsPresent: function () {
            if (typeof google === 'object'
                    && typeof google.maps === 'object'
                    && typeof google.maps.places === 'object') {
                return true;
            }
            return false;
        },
        
        setupAutocomplete: function () {
            
            var input = $("#ccm-google-map-block-location > input[id=location]").get(0),
                autocomplete = new google.maps.places.Autocomplete(input),
                note = $("#ccm-google-map-block-location > #block_note").get(0);
            input.onchange = function () {
                this.className = 'notfound form-control ccm-input-text';
            };
            
            // Keeps the autocomplete list visible above modal dialogue
            window.C5GMaps.pacTimer = setInterval(function () {
                var cntr = $('.pac-container'), locBox = $('#location');
                cntr.css('z-index', '2000');
                if (locBox.length === 0) {
                    clearInterval(window.C5GMaps.pacTimer);
                    cntr.remove();
                }
            }, 250);

            google.maps.event.addDomListener(input, 'keydown', function(e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                }
            });
            google.maps.event.addListener(autocomplete, 'place_changed', function () {

                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    // Inform the user that the place was not found and return.
                    input.className = 'notfound';
                    note.innerHTML = 'The place you entered could not be found.';
                    return;
                } else {
                    $('#ccm-google-map-block-location > input[id=latitude]').val(place.geometry.location.lat());
                    $('#ccm-google-map-block-location > input[id=longitude]').val(place.geometry.location.lng());
                    input.className = 'form-control ccm-input-text';
                    note.innerHTML = '';
                }
                
            });
            
            
        }
    };

}());