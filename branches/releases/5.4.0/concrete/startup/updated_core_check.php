<?php 

$td = substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), DIRECTORY_SEPARATOR . 'startup'));
if (defined('DIRNAME_APP_UPDATED') && $td != DIR_BASE . DIRECTORY_SEPARATOR . DIRNAME_UPDATES . DIRECTORY_SEPARATOR . DIRNAME_APP_UPDATED . DIRECTORY_SEPARATOR . DIRNAME_APP) {
	require(DIR_BASE . '/' . DIRNAME_UPDATES . '/' . DIRNAME_APP_UPDATED . '/' . DIRNAME_APP . '/' . 'dispatcher.php');
}
