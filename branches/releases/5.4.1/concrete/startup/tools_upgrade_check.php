<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
	
	$co = Request::get();
	if ($co->isIncludeRequest() && $co->getFilename() == 'upgrade.php') {
		if (file_exists(DIR_FILES_TOOLS . '/' . $co->getFilename())) {
			include(DIR_FILES_TOOLS . '/' . $co->getFilename());
		} else if (file_exists(DIR_FILES_TOOLS_REQUIRED . '/' . $co->getFilename())) {
			include(DIR_FILES_TOOLS_REQUIRED . '/' .  $co->getFilename());
		}
		require(DIR_BASE_CORE . '/startup/shutdown.php');
		exit;
	}