<?

if (defined('DIRNAME_APP_UPDATED') && dirname(__FILE__) != DIR_BASE . '/' . DIRNAME_UPDATES . '/' . DIRNAME_APP_UPDATED . '/' . DIRNAME_APP) {
	require(DIR_BASE . '/' . DIRNAME_UPDATES . '/' . DIRNAME_APP_UPDATED . '/' . DIRNAME_APP . '/' . 'dispatcher.php');
}
