<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
<style type="text/css">
table#googleMapBlockSetup th {font-weight: bold; text-style: normal; padding-right: 8px; white-space: nowrap; vertical-align:top ; padding-bottom:8px}
table#googleMapBlockSetup td{ font-size:12px; vertical-align:top; padding-bottom:8px;}
</style> 

<table id="googleMapBlockSetup" width="100%"> 
	<tr>
		<th><?php echo t('Map Title')?>: <div class="note">(<?php echo t('Optional')?>)</div></th>
		<td><input id="ccm_googlemap_block_title" name="title" value="<?php echo $mapObj->title?>" maxlength="255" type="text" style="width:100%"></td>
	</tr>	
	<tr>
		<th><?php echo t('Google Maps API Key')?>:</th>
		<td>
			<input id="ccm_googlemap_block_api_key" name="api_key" value="<?php echo $mapObj->api_key?>" maxlength="255" type="text" style="width:100%">
			<div class="note"><a href="http://code.google.com/apis/maps/signup.html" target="_blank"><?php echo t('Sign up for your key')?></a></div>
		</td>
	</tr>
	<tr>
		<th><?php echo t('Location')?>:</th>
		<td>
		<input id="ccm_googlemap_block_location" name="location" value="<?php echo $mapObj->location?>" maxlength="255" type="text" style="width:100%">
		<div class="note"><?php echo t('e.g. 222 NW Davis St. Portland, OR')?></div>
		</td>
	</tr>
	<tr>
		<th><?php echo t('Zoom')?>:</th>
		<td>
		<input id="ccm_googlemap_block_zoom" name="zoom" value="<?php echo $mapObj->zoom?>" maxlength="255" type="text" style="width:100%">
		<div class="ccm-note"><?php echo t('Enter a number from 0 to 17, with 17 being the most zoomed in.')?> </div>
		</td>
	</tr>			
</table>