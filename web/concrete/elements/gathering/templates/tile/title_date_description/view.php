<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-gathering-tile-title-description">
	<div class="ccm-gathering-tile-headline"><a href="<?=$link?>"><?=$title?></a></div>
	<div class="ccm-gathering-tile-date"><?=date(DATE_APP_GENERIC_MDYT_FULL, strtotime($date_time))?></div>
	<div class="ccm-gathering-tile-description">
	<?=$description?>
	</div>
</div>



