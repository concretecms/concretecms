<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-gathering-overlay">
	<div class="ccm-gathering-overlay-title"><?=$title?></div>
	<? if ($date_time) { ?>
		<div><?=Core::make('helper/date')->formatDateTime($date_time, true)?></div>
	<? } ?>
	<? if ($description) { ?>
		<p><?=$description?></p>
	<? } ?>
</div>
