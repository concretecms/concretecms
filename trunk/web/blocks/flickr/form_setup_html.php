<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<style>
table#FlickrSetup th {font-weight: bold; text-style: normal; padding-right: 8px; white-space: nowrap}
table#FlickrSetup td{ font-size:12px }

</style> 

<table id="FlickrSetup" width="100%">
	<tr>
		<th><?=t('Flickr Feed URL')?>:</th>
		<td width="100%">
			<input id="ccm_flickr_url" name="url" value="<?=$controllerObj->url?>" maxlength="255" type="text" style="width:100%"><br />
			<div class="ccm-note"><?=t('This should be RSS feed url on one of the pages on flickr.')?></div>
		</td>
	</tr>
	<tr>
		<th><?=t('Title')?>: (<?=t('Optional')?>)</th>
		<td><input id="ccm_flickr_title" name="title" value="<?=$controllerObj->title?>" maxlength="255" type="text" style="width:100%"></td>
	</tr>	
	<tr>
		<th><?=t('Number of images to display')?>:</th>
		<td><input id="ccm_flickr_itemsToDisplay"  name="itemsToDisplay" value="<?=intval($controllerObj->itemsToDisplay)?>" type="text" size="2" maxlength="3"></td>
	</tr>
	<tr>
		<th><?=t('Max Width')?>:</th>
		<td>
			<input id="ccm_flickr_max_width" name="maxWidth" value="<?=$controllerObj->maxWidth?>" maxlength="8" type="text" >
		</td>
	</tr>
	<tr>
		<th><?=t('Max Height')?>:</th>
		<td>
			<input id="ccm_flickr_max_height" name="maxHeight" value="<?=$controllerObj->maxHeight?>" maxlength="8" type="text" >
		</td>
	</tr> 		
</table>