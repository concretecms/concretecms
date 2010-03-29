<?

if (defined('DIRNAME_APP_UPDATED') && (!isset($GLOBALS['APP_UPDATED_PASSTHRU']) || $GLOBALS['APP_UPDATED_PASSTHRU'] == false)) {
	$GLOBALS['APP_UPDATED_PASSTHRU'] = true;
	require(DIR_BASE . '/' . DIRNAME_UPDATES . '/' . DIRNAME_APP_UPDATED . '/' . DIRNAME_APP . '/' . 'dispatcher.php');
	exit;
}