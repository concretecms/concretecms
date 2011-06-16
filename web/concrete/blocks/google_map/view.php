<? defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
if ($c->isEditMode()) { ?>
	<div class="ccm-edit-mode-disabled-item" style="height: 400px">
		<div style="padding: 80px 0px 0px 0px"><?=t('Google Map disabled in edit mode.')?></div>
	</div>
<? } else { ?>
	<style type="text/css">
	.googleMapCanvas{ width:100%; border:0px none; height: 400px;}
	</style>
	<script type="text/javascript"> 
	function googleMapInit<?=$bID?>() { 
		try{
			var latlng = new google.maps.LatLng(<?=$latitude?>, <?=$longitude?>);
		    var mapOptions = {
		      zoom: <?=$zoom?>,
		      center: latlng,
		      mapTypeId: google.maps.MapTypeId.ROADMAP,
		      streetViewControl: false,
		      mapTypeControl: false
			};
		    var map = new google.maps.Map(document.getElementById('googleMapCanvas<?=$bID?>'), mapOptions);
		    var marker = new google.maps.Marker({
		        position: latlng, 
		        map: map
		    });
		}catch(e){alert(e.message)} 
	}
	</script>
	
	<? if( strlen($title)>0){ ?><h3><?=$title?></h3><? } ?>
	<div id="googleMapCanvas<?=$bID?>" class="googleMapCanvas"></div>
	<script type="text/javascript">$(function() {
		googleMapInit<?=$bID?>();
	});</script>
	
<? } ?>