<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<style type="text/css">
table#rssDisplayerSetup th {font-weight: bold; text-style: normal; padding-right: 8px; white-space: nowrap}
table#rssDisplayerSetup td{ font-size:12px }

</style> 

<?php 

if (!$dateFormat) {
	$dateFormat = t('F jS');
}
?>

<table id="rssDisplayerSetup" width="100%">
	<tr>
		<th><?php echo t('Feed URL')?>:</th>
		<td width="100%"><input id="ccm_rss_displayer_url" name="url" value="<?php echo $rssObj->url?>" maxlength="255" type="text" style="width:95%"></td>
	</tr>
	<tr>
		<th><?php echo t('Date Format')?>:</th>
		<td><input type="text" name="dateFormat" value="<?php echo $dateFormat?>" style="width: 95%" />
			<div class="ccm-note">(<?php echo t('Enter a <a href="%s" target="_blank">PHP date string</a> here.', 'http://www.php.net/date')?>)</div>
		</td>
	<tr>
		<th><?php echo t('Feed Title')?>: (<?php echo t('Optional')?>)</th>
		<td><input id="ccm_rss_displayer_title" name="title" value="<?php echo $rssObj->title?>" maxlength="255" type="text" style="width:95%"></td>
	</tr>	
	<tr>
		<th><?php echo t('Number of items to display')?>:</th>
		<td><input id="ccm_rss_displayer_itemsToDisplay"  name="itemsToDisplay" value="<?php echo intval($rssObj->itemsToDisplay)?>" type="text" size="2" maxlength="3"></td>
	</tr>
	<tr>
		<th><?php echo t('Display')?>:</th>
		<td>
			<input name="showSummary" type="radio" value="0" <?php echo (!$rssObj->showSummary)?'checked':''?>><?php echo t('Only Titles')?>&nbsp; <br>
			<input name="showSummary" type="radio" value="1" <?php echo ($rssObj->showSummary)?'checked':''?>><?php echo t('Titles & Summary')?>
		</td>
	</tr>
	<tr>
		<th><?php echo t('Open links in a new window')?></th>
		<td>
			<input name="launchInNewWindow" type="checkbox" value="1" <?php echo ($rssObj->launchInNewWindow)?'checked':''?>>
		</td>
	</tr>		
</table>