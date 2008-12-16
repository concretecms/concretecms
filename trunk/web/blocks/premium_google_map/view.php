<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<style>
#googleAdvancedMapCanvas<?=$bID?>{ width:<?=($w)?$w:'100%'?>; border:0px none; height:<?=($h)?$h:'400px'?>;}
</style>   
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?=$api_key?>" type="text/javascript"></script> 
<script type="text/javascript"> 
var geoXml<?=$bID?>=null;
function googleMapInit<?=$bID?>() { 
	try{
		if (GBrowserIsCompatible()) { 
			//geoXml = new EGeoXml( "http://mapgadgets.googlepages.com/cta.kml" );
			var map = new GMap2(document.getElementById("googleAdvancedMapCanvas<?=$bID?>"));
			map.setCenter(new GLatLng( <?=$latitude?>, <?=$longitude?>), <?=$zoom?> ); 
			
			//var mapControl = new GSmallMapControl();
			//map.addControl(mapControl); 
			//  G_NORMAL_MAP  G_HYBRID_MAP  G_SATELLITE_MAP  G_PHYSICAL_MAP  G_SATELLITE_3D_MAP
			map.setMapType(<?=$map_type_constant?>);
			<? if($show_earth){ ?>
			map.addMapType(G_SATELLITE_3D_MAP);			
			<? } ?>
		    map.addControl(new GHierarchicalMapTypeControl());
		    map.addControl(new GLargeMapControl());							
			<? if( strlen($kml_file_path) ){ ?>
			geoXml<?=$bID?> = new GGeoXml("<?=$kml_file_path ?>"
				<? if( floatval($latitude)!=0 && floatval($longitude)!=0 ){ ?>
				,function(){ geoXml<?=$bID?>.gotoDefaultViewport(map); } 			
				<? } ?>
			);			
			map.addOverlay(geoXml<?=$bID?>);
			<? } ?>
		}
	}catch(e){alert(e.message)} 
}
</script>

<? if( strlen($title)>0){ ?><h3><?=$title?></h3><? } ?>

<div id="googleAdvancedMapCanvas<?=$bID?>" class="googleAdvancedMapCanvas"></div>
<? if(strlen($kml_file_path)){ ?>
	<div class="ccm-note"><a href="<?=$kml_file_path ?>" target="_blank">Download KML for Google Earth</a></div>
<? } ?>

<script type="text/javascript">$(function() {
	googleMapInit<?=$bID?>();
});</script> 