<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<?
global $c;
if ($c->isEditMode() && $show_earth) { ?>

	<div style="border:1px solid #999; background:#eee; color:#999; padding:64px 16px; text-align:center; margin:8px 0px">
		<?= t('Google Earth Map Placeholder') ?>
	</div>

<? }else{ ?>
	
	<style>
	#googleAdvancedMapCanvas<?=$bID?>{ width:<?=($w)?$w:'100%'?>; border:0px none; height:<?=($h)?$h:'400px'?>;}
	</style>   
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?=$api_key?>" type="text/javascript"></script> 
	
	<? if($show_earth){ ?>
	<script type="text/javascript" src="http://www.google.com/jsapi?key=<?=$api_key?>"></script> 
	<script>
	google.load("earth","1"); 
	function detectGoogleEarth(){
		try{
		var isInstalled = google.earth.isInstalled(); 
		var isSupported = google.earth.isSupported();
		if(isInstalled && isSupported) return true;
		}catch(e){  } 
		return false;
	} 
	</script>
	<? } ?>
	
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
				<? if($show_earth){ ?>
				if(detectGoogleEarth())
					 map.setMapType(G_SATELLITE_3D_MAP);
				else map.setMapType(<?=$map_type_constant?>);
				<? }else{ ?> 
				map.setMapType(<?=$map_type_constant?>);
				<? } ?>
				
				<? if($show_earth){ ?>
				if(detectGoogleEarth()) map.addMapType(G_SATELLITE_3D_MAP);			
				<? } ?>
				
				map.addControl(new GHierarchicalMapTypeControl());
				map.addControl(new GLargeMapControl());							
				<? if( strlen($kml_file_path) ){ ?>
				geoXml<?=$bID?> = new GGeoXml("<?=$kml_file_path ?>"
					<? if( floatval($latitude)==0 && floatval($longitude)==0 ){ ?>
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
		<div class="ccm-note"><a href="<?=$this->url('/tools/blocks/premium_google_map/download.php?bID='.$bID) ?>" target="_blank">Download KML for Google Earth</a></div>
	<? } ?>
	
	<script type="text/javascript">$(function() {
		googleMapInit<?=$bID?>();
	});</script> 
	
<? } ?>