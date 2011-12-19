<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<style type="text/css">
table#rssDisplayerSetup th {font-weight: bold; text-style: normal; padding-right: 8px; white-space: nowrap}
table#rssDisplayerSetup td{ font-size:12px }

</style> 

<?

if (!$rssObj->dateFormat) {
	$rssObj->dateFormat = t('F jS');
}
?>

<div class="clearfix">
	<label><?=t('Feed URL')?>:</label>
	<div class="input"><input id="ccm_rss_displayer_url" name="url" value="<?=$rssObj->url?>" maxlength="255" type="text"></div>
</div>

<div class="clearfix">
	<label><?=t('Date Format')?>:</label>
	<div class="input"><input type="text" name="dateFormat" value="<?=$rssObj->dateFormat?>" />
		<div class="help-block">(<?=t('Enter a <a href="%s" target="_blank">PHP date string</a> here.', 'http://www.php.net/date')?>)</div>

	</div>
</div>

<div class="clearfix">
	<label><?=t('Feed Title')?>: (<?=t('Optional')?>)</label>
	<div class="input">
		<input id="ccm_rss_displayer_title" name="title" value="<?=$rssObj->title?>" maxlength="255" type="text" />
	</div>
</div>

<div class="clearfix">
	<label><?=t('# items to display')?>:</label>
	<div class="input">
		<input id="ccm_rss_displayer_itemsToDisplay"  name="itemsToDisplay" value="<?=intval($rssObj->itemsToDisplay)?>" type="text" size="2" maxlength="3" />
	</div>
</div>

<div class="clearfix">
	<label><?=t('Display')?>:</label>
	<div class="input">
	<ul class="inputs-list">
		<li><label><input name="showSummary" type="radio" value="0" <?=(!$rssObj->showSummary)?'checked':''?>> <span><?=t('Only Titles')?></span></li>
		<li><label><input name="showSummary" type="radio" value="1" <?=($rssObj->showSummary)?'checked':''?>> <span><?=t('Titles & Summary')?></span></li>
	</ul>
	</div>
</div>

<div class="clearfix">
	<label></label>
	<div class="input">
	<ul class="inputs-list">
		<li><label><input name="launchInNewWindow" type="checkbox" value="1" <?=($rssObj->launchInNewWindow)?'checked':''?>> <span><?=t('Open links in a new window')?></span></label></li>
	</ul>
	</div>
</div>