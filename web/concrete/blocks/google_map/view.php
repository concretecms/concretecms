<?php defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
if ($c->isEditMode()) { ?>
	<div class="ccm-edit-mode-disabled-item" style="width: <?= $width; ?>; height: <?= $height; ?>">
		<div style="padding: 80px 0px 0px 0px"><?= t('Google Map disabled in edit mode.')?></div>
	</div>
<?php  } else { ?>
	<?php  if( strlen($title)>0){ ?><h3><?= $title?></h3><?php  } ?>
	<div id="googleMapCanvas<?= $bID?>" class="googleMapCanvas" style="width: <?= $width; ?>; height: <?= $height; ?>"></div>
<?php  } ?>



<?php
/*
    Note - this goes in here because it's the only way to preserve block caching for this block. We can't
    set these values through the controller
*/
?>

<script type="text/javascript">
    function googleMapInit<?=$bID?>() {
        try{
            var latlng = new google.maps.LatLng(<?=$latitude?>, <?=$longitude?>);
            var mapOptions = {
                zoom: <?=$zoom?>,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                streetViewControl: false,
                scrollwheel: <?=!!$scrollwheel ? "true" : "false"?>,
                mapTypeControl: false
            };
            var map = new google.maps.Map(document.getElementById('googleMapCanvas<?=$bID?>'), mapOptions);
            var marker = new google.maps.Marker({
                position: latlng,
                map: map
            });
        }catch(e){
            $("#googleMapCanvas<?=$bID?>").replaceWith("<p>Unable to display map: "+e.message+"</p>")}
    }
    $(function() {
        var t;
        var startWhenVisible = function (){
            if ($("#googleMapCanvas<?=$bID?>").is(":visible")){
                window.clearInterval(t);
                googleMapInit<?=$bID?>();
                return true;
            }
            return false;
        };
        if (!startWhenVisible()){
            t = window.setInterval(function(){startWhenVisible();},100);
        }
    });
</script>
