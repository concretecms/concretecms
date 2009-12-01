<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<style>
table#rssDisplayerSetup th {font-weight: bold; text-style: normal; padding-right: 8px; white-space: nowrap}
table#rssDisplayerSetup td{ font-size:12px }

</style> 

<table id="rssDisplayerSetup" width="100%">
	<tr>
		<th><?=t('Feed URL')?>:</th>
		<td width="100%"><input id="ccm_rss_displayer_url" name="url" value="<?=$rssObj->url?>" maxlength="255" type="text" style="width:95%"></td>
	</tr>
	<tr>
		<th><?=t('Feed Title')?>: (<?=t('Optional')?>)</th>
		<td><input id="ccm_rss_displayer_title" name="title" value="<?=$rssObj->title?>" maxlength="255" type="text" style="width:95%"></td>
	</tr>	
	<tr>
		<th><?=t('Number of items to display')?>:</th>
		<td><input id="ccm_rss_displayer_itemsToDisplay"  name="itemsToDisplay" value="<?=intval($rssObj->itemsToDisplay)?>" type="text" size="2" maxlength="3"></td>
	</tr>
	<tr>
		<th><?=t('Display')?>:</th>
		<td>
			<input name="showSummary" type="radio" value="0" <?=(!$rssObj->showSummary)?'checked':''?>><?=t('Only Titles')?>&nbsp; <br>
			<input name="showSummary" type="radio" value="1" <?=($rssObj->showSummary)?'checked':''?>><?=t('Titles & Summary')?>
		</td>
	</tr>
	<tr>
		<th><?=t('Open links in a new window')?></th>
		<td>
			<input name="launchInNewWindow" type="checkbox" value="1" <?=($rssObj->launchInNewWindow)?'checked':''?>>
		</td>
	</tr>		
</table>