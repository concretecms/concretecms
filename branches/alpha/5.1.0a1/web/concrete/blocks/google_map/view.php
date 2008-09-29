<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<style>
.googleMapCanvas{ width:100%; border:0px none; height: 400px;}
</style>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?=$api_key?>" type="text/javascript"></script>
<script type="text/javascript"> 
function googleMapInit<?=$bID?>() { 
	try{
		if (GBrowserIsCompatible()) { 
			var map = new GMap2(document.getElementById("googleMapCanvas<?=$bID?>"));
			map.setCenter(new GLatLng(<?=$latitude?>, <?=$longitude?>), <?=$zoom?>); 
			<? if($latitude!=0 && $longitude!=0){ ?>
			var point = new GLatLng(<?=$latitude?>,<?=$longitude?>);
			map.addOverlay(new GMarker(point));
			<? } ?> 
			var mapControl = new GSmallMapControl();
			map.addControl(mapControl); 
		}
	}catch(e){alert(e.message)} 
}
</script>

<? if( strlen($title)>0){ ?><h3><?=$title?></h3><? } ?>

<div id="googleMapCanvas<?=$bID?>" class="googleMapCanvas"></div>
<script type="text/javascript">$(function() {
	googleMapInit<?=$bID?>();
});</script>