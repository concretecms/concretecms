<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	
	$co = Request::get();
	if ($co->isIncludeRequest() && $co->getFilename() == 'upgrade.php') {
		include(DIR_FILES_TOOLS_REQUIRED . '/' .  $co->getFilename());
		require(DIR_BASE_CORE . '/startup/shutdown.php');
		exit;
	}