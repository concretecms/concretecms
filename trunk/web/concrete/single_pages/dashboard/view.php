<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-module-wrapper">
<div id="ccm-module-row1">
	<? if (is_object($modules[0])) { ?>
	<div class="ccm-module">
		<h1><span><?=t($modules[0]->dbhDisplayName)?></span></h1>
		<div class="ccm-dashboard-inner"><?=$dh->output($modules[0])?></div>
	</div>
	<? } ?>
	<? if (is_object($modules[1])) { ?>
	<div class="ccm-module">
		<h1><span><?=t($modules[1]->dbhDisplayName)?></span></h1>
		<div class="ccm-dashboard-inner"><?=$dh->output($modules[1])?></div>
	</div>
	<? } ?>
</div>
<div id="ccm-module-row2">
	<? if (is_object($modules[2])) { ?>
	<div class="ccm-module">
		<h1><span><?=t($modules[2]->dbhDisplayName)?></span></h1>
		<div class="ccm-dashboard-inner"><?=$dh->output($modules[2])?></div>
	</div>
	<? } ?>
	<? if (is_object($modules[3])) { ?>
	<div class="ccm-module">
		<h1><span><?=t($modules[3]->dbhDisplayName)?></span></h1>
		<div class="ccm-dashboard-inner"><?=$dh->output($modules[3])?></div>
	</div>
	<? } ?>
	<? if (is_object($modules[4])) { ?>
	<div class="ccm-module">
		<h1><span><?=t($modules[4]->dbhDisplayName)?></span></h1>
		<div class="ccm-dashboard-inner"><?=$dh->output($modules[4])?></div>
	</div>
	<? } ?>

</div>
</div>