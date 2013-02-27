<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
if (is_object($configuration)) { 
	$rssFeedURL = $configuration->getRSSFeedURL();
}
?>
<fieldset>
	<div class="control-group">
		<label class="control-label"><?=t('RSS Feed')?></label>
		<div class="controls">
			<?=$form->text('rssFeedURL', $rssFeedURL)?>
		</div>
	</div>
</fieldset>

