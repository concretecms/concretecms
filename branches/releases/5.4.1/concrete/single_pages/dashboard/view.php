<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-module-wrapper">
<div id="ccm-module-row1">
	<?php  if (is_object($modules[0])) { ?>
	<div class="ccm-module">
		<h1><span><?php echo t($modules[0]->dbhDisplayName)?></span></h1>
		<div class="ccm-dashboard-inner"><?php echo $dh->output($modules[0])?></div>
	</div>
	<?php  } ?>
	<?php  if (is_object($modules[1])) { ?>
	<div class="ccm-module">
		<h1><span><?php echo t($modules[1]->dbhDisplayName)?></span></h1>
		<div class="ccm-dashboard-inner"><?php echo $dh->output($modules[1])?></div>
	</div>
	<?php  } ?>
</div>
<div id="ccm-module-row2">
	<?php  if (is_object($modules[2])) { ?>
	<div class="ccm-module">
		<h1><span><?php echo t($modules[2]->dbhDisplayName)?></span></h1>
		<div class="ccm-dashboard-inner"><?php echo $dh->output($modules[2])?></div>
	</div>
	<?php  } ?>
	<?php  if (is_object($modules[3])) { ?>
	<div class="ccm-module">
		<h1><span><?php echo t($modules[3]->dbhDisplayName)?></span></h1>
		<div class="ccm-dashboard-inner"><?php echo $dh->output($modules[3])?></div>
	</div>
	<?php  } ?>
	<?php  if (is_object($modules[4])) { ?>
	<div class="ccm-module">
		<h1><span><?php echo t($modules[4]->dbhDisplayName)?></span></h1>
		<div class="ccm-dashboard-inner"><?php echo $dh->output($modules[4])?></div>
	</div>
	<?php  } ?>

</div>
</div>