<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-google-map-block-container row">
    <div class="col-xs-12">
        <div class="form-group">
            <?php echo $form->label('apiKey', t('API Key')); ?>
            <?php echo $form->text('apiKey', Config::get('app.api_keys.google.maps')); ?>
        </div>

        <div class="form-group">
            <div role="button" id="ccm-google-map-key" class="btn btn-default"><?php echo t('Check API Key'); ?>
                <i id="check-spinner" class="fa fa-play"></i>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label('title', t('Map Title (optional)'));?>
            <?php echo $form->text('title', $title);?>
        </div>

        <div id="ccm-google-map-block-location" class="form-group">
            <?php echo $form->label('location', t('Location'));?>
            <?php echo $form->text('location', $location);?>
            <?php echo $form->hidden('latitude', $latitude);?>
            <?php echo $form->hidden('longitude', $longitude);?>
            <div id="block_note" class="note"><?php echo t('Start typing a location (e.g. Apple Store or 235 W 3rd, New York) then click on the correct entry on the list.')?></div>
            <div id="map-canvas"></div>
        </div>
    </div>

    <div class="col-xs-4">
        <div class="form-group">
            <?php echo $form->label('zoom', t('Zoom'));?>
            <?php
            $zoomArray = array();
            for ($i = 0; $i <= 21; $i++) {
                $zoomArray[$i] = $i;
            }
            ?>
            <?php echo $form->select('zoom', $zoomArray, $zoom ? $zoom : 14);?>
        </div>
    </div>

    <div class="col-xs-4">
        <div class="form-group">
            <?php echo $form->label('width', t('Map Width'));?>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-arrows-h"></i></span>
                <?php if(is_null($width) || $width == 0) {$width = '100%';};?>
                <?php echo $form->text('width', $width);?>
            </div>
        </div>
    </div>

    <div class="col-xs-4">
        <div class="form-group">
            <?php echo $form->label('height', t('Map Height'));?>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-arrows-v"></i></span>
                <?php if(is_null($height) || $height == 0) {$height = '400px';};?>
                <?php echo $form->text('height', $height); ?>
            </div>
        </div>
    </div>

    <div class="col-xs-12">
        <div class="form-group">
          <label>
            <?php echo $form->checkbox('scrollwheel', 1, (is_null($scrollwheel) || $scrollwheel)); ?>
            <?php echo t("Enable Scroll Wheel")?>
          </label>
        </div>
    </div>
</div>

<script>
var validKey;
function gm_authFailure() {
    $('#check-spinner').removeClass('fa-refresh fa-spin').addClass('fa-play');
    alert('<?php echo t('Invalid API Key'); ?>');
    validKey = false;
}

(function () {
    "use strict";

    var checkKey = function() {
        $('#ccm-google-map-key').click(function() {
            validKey = true;
            $('#check-spinner').removeClass('fa-play').addClass('fa-refresh fa-spin');

            $('#location').removeAttr('placeholder autocomplete disabled style');
            $('#location').removeClass('gm-err-autocomplete');

            var apiKey = $('#apiKey').val().trim();
            if ($('#apiKeyCheck')) {
                $('#apiKeyCheck').remove();
            }
            $('body').append('<script id="apiKeyCheck" src="https://maps.googleapis.com/maps/api/js?' +
                'key=' + apiKey +
                '&libraries=places" <\/script>'
            );
            setTimeout(function() {
                if (validKey) {
                    window.C5GMaps.init();
                    isValidKey();
                }
            }, 10000);
        });
    };
    checkKey();

    var isValidKey = function() {
        if ($('#location')[0].hasAttribute('placeholder')) {
            setTimeout(function() {
                if (!$('#location').hasClass('gm-err-autocomplete')) {
                    $('#check-spinner').removeClass('fa-refresh fa-spin').addClass('fa-play');
                    alert('<?php echo t('Valid API Key'); ?>');
                }
            }, 5000)
        } else {
            setTimeout(function() {
                isValidKey();
            }, 50);
        }
    };

    window.C5GMaps = {

        pacTimer: null,

        init: function () {
            window.C5GMaps.setupAutocomplete();
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
</script>
