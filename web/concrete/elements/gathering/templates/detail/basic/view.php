<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-gathering-overlay">
	<div class="ccm-gathering-overlay-title"><?=$title?></div>
	<? if ($date_time) { ?>
		<div><?=date(DATE_APP_GENERIC_MDYT_FULL, strtotime($date_time))?></div>
	<? } ?>
	<? if ($description) { ?>
		<p><?=$description?></p>
	<? } ?>
</div>
