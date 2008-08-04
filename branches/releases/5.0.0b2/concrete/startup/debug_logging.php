<?

$debug_level = Config::get('SITE_DEBUG_LEVEL');
switch($debug_level) {
	case DEBUG_DISPLAY_ERRORS_SQL:
		$db = Loader::db();
		$db->setDebug(true);
		error_reporting(E_ALL ^ E_NOTICE);
		ini_set('display_errors', 1);
		break;
	case DEBUG_DISPLAY_ERRORS:
		error_reporting(E_ALL ^ E_NOTICE);
		ini_set('display_errors', 1);
		break;
	case DEBUG_DISPLAY_PRODUCTION:
		error_reporting(E_ALL ^ E_NOTICE);
		ini_set('display_errors', 0);
		break;
}