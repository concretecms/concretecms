<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>  
<?
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
if(intval($mapObj->kml_fID)>0){ 
	$bf = $mapObj->getFileObject();
}
?>
<style>
table#googleMapBlockSetup th {font-weight: bold; text-style: normal; padding-right: 8px; white-space: nowrap; vertical-align:top ; padding-bottom:8px}
table#googleMapBlockSetup td{ font-size:12px; vertical-align:top; padding-bottom:8px;}
</style> 

<table id="googleMapBlockSetup" width="100%"> 
	<tr>
		<th><?=t('Map Title')?>: <div class="note">(<?=t('Optional')?>)</div></th>
		<td><input id="ccm_googlemap_block_title" name="title" value="<?=$mapObj->title?>" maxlength="255" type="text" style="width:100%"></td>
	</tr>	
	<tr>
		<th><?=t('Google Maps API Key')?>:</th>
		<td>
			<input id="ccm_googlemap_block_api_key" name="api_key" value="<?=$mapObj->api_key?>" maxlength="255" type="text" style="width:100%">
			<div class="ccm-note"><a href="http://code.google.com/apis/maps/signup.html" target="_blank"><?=t('Sign up for your key')?></a></div>
		</td>
	</tr>
	<tr>
		<th><?=t('Default View')?>:</th>
		<td>
		<select id="ccm_googlemap_block_map_type" name="map_type">
			<? foreach($mapObj->map_types as $map_type_key=>$constant){ ?>
			<option value="<?=$map_type_key?>" <?=($map_type_key==$mapObj->map_type)?'selected':'' ?>><?=$map_type_key?></option>
			<? } ?>
		</select>
		</td>
	</tr>
	<tr>
		<th><?=t('Show Google Earth Button')?>:</th>
		<td>
		<input type="checkbox" value="1" name="show_earth" <?=($mapObj->show_earth)?'checked':'' ?> />
		</td>
	</tr>		
	<tr>
		<th><?=t('Width')?>:</th>
		<td>
		<input id="ccm_googlemap_block_w" name="w" value="<?=$mapObj->w?>" maxlength="255" type="text" size="6"> 
		</td>
	</tr>
	<tr>
		<th><?=t('Height')?>:</th>
		<td>
		<input id="ccm_googlemap_block_h" name="h" value="<?=$mapObj->h?>" maxlength="255" type="text" size="6"> 
		</td>
	</tr>
	<tr>
		<th><?=t('Zoom')?>:</th>
		<td>
		<input id="ccm_googlemap_block_zoom" name="zoom" value="<?=$mapObj->zoom?>" maxlength="255" type="text">
		<div class="ccm-note"><?=t('Enter a number from 0 to 17, with 17 being the most zoomed in.')?> </div>
		</td>
	</tr>
	<tr>
		<th><?=t('Latitude')?>:</th>
		<td>
		<input id="ccm_googlemap_block_latitude" name="latitude" value="<?=$mapObj->latitude?>" maxlength="255" type="text">
		</td>
	</tr>
	<tr>
		<th><?=t('Longitude')?>:</th>
		<td>
		<input id="ccm_googlemap_block_longitude" name="longitude" value="<?=$mapObj->longitude?>" maxlength="255" type="text">
		</td>
	</tr>			
	<tr>
		<th><?=t('KML Upload')?>:</th>
		<td>
		<?=$al->file('ccm-b-file', 'fID', t('Choose File'), $bf);?></div>
		<div class="ccm-note">
			<a href="http://code.google.com/apis/kml/documentation/" target="_blank"><?=t("What's a KML file?")?></a><br />
			<?=t('(For your KML file to work, it has to be accessible by google over the internet)'); ?>
		</div>
		</td>
	</tr>
	
</table>