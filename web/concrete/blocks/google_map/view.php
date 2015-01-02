<? defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
if ($c->isEditMode()) { ?>
	<div class="ccm-edit-mode-disabled-item" style="width: <? echo $width; ?>; height: <? echo $height; ?>">
		<div style="padding: 80px 0px 0px 0px"><? echo t('Google Map disabled in edit mode.')?></div>
	</div>
<?  } else { ?>	
	<?  if( strlen($title)>0){ ?><h3><? echo $title?></h3><?  } ?>
	<div id="googleMapCanvas<? echo $bID?>" class="googleMapCanvas" style="width: <? echo $width; ?>; height: <? echo $height; ?>"></div>	
<?  } ?>



<?
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
                scrollwheel: <?= !!$scrollwheel ? "true" : "false" ?>,
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
