<?php
defined('C5_EXECUTE') or die('Access Denied.');

if (defined('DIRNAME_APP_UPDATED') && (!isset($GLOBALS['APP_UPDATED_PASSTHRU']) || $GLOBALS['APP_UPDATED_PASSTHRU'] == false)) {
	$GLOBALS['APP_UPDATED_PASSTHRU'] = true;
	if (is_dir(DIR_BASE . '/' . DIRNAME_UPDATES . '/' . DIRNAME_APP_UPDATED)) {
		require(DIR_BASE . '/' . DIRNAME_UPDATES . '/' . DIRNAME_APP_UPDATED . '/' . DIRNAME_APP . '/' . 'dispatcher.php');
	} else if(file_exists(DIRNAME_UPDATES . '/' . DIRNAME_APP_UPDATED . '/' . DIRNAME_APP . '/' . 'dispatcher.php')){
		require(DIRNAME_UPDATES . '/' . DIRNAME_APP_UPDATED . '/' . DIRNAME_APP . '/' . 'dispatcher.php');
	} else {
		die(sprintf('Invalid "%s" defined. Please remove it from %s.','DIRNAME_APP_UPDATED', CONFIG_FILE));
	}
	exit;
}