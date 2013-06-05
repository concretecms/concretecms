<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<style type="text/css">
table#googleMapBlockSetup th {font-weight: bold; text-style: normal; padding-right: 8px; white-space: nowrap; vertical-align:top ; padding-bottom:8px}
table#googleMapBlockSetup td{ font-size:12px; vertical-align:top; padding-bottom:8px;}
</style> 

<table id="googleMapBlockSetup" width="100%" class="table table-bordered"> 
	<tr>
		<th><?=t('Map Title')?>: <div class="note">(<?=t('Optional')?>)</div></th>
		<td><input id="ccm_googlemap_block_title" name="title" value="<?=$mapObj->title?>" maxlength="255" type="text" style="width:80%"></td>
	</tr>	
	<tr>
		<th><?=t('Location')?>:</th>
		<td>
		<input id="ccm_googlemap_block_location" name="location" value="<?=$mapObj->location?>" maxlength="255" type="text" style="width:80%">
		<div class="note"><?=t('e.g. 17 SE 3rd #410, Portland, OR, 97214')?></div>
		</td>
	</tr>
	<tr>
		<th><?=t('Zoom')?>:</th>
		<td>
		<input id="ccm_googlemap_block_zoom" name="zoom" value="<?=$mapObj->zoom?>" maxlength="255" type="text" style="width:80%">
		<div class="ccm-note"><?=t('Enter a number from 0 to 21, with 21 being the most zoomed in.')?> </div>
		</td>
	</tr>			
</table>