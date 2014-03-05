<?
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

	if (ENABLE_AUTO_UPDATE_CORE === true && version_compare(APP_VERSION, Config::get('SITE_APP_VERSION'), '>')) {
		$updatesToApply = Loader::helper('concrete/upgrade')->getList(Config::get('SITE_APP_VERSION'));
		foreach($updatesToApply as $ugh) {
			if (method_exists($ugh, 'prepare')) {
				$ugh->prepare();
			}
			if (isset($ugh->dbRefreshTables) && count($ugh->dbRefreshTables) > 0) {
				Loader::helper('concrete/upgrade')->refreshDatabaseTables($ugh->dbRefreshTables);
			}
			if (method_exists($ugh, 'run')) {
				$ugh->run();
			}
		}
		Config::save('SITE_APP_VERSION', APP_VERSION);
	}
