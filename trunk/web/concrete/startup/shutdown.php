<?
$db = Loader::db();
if (is_object($db)) {
	
	$debug_level = Config::get('SITE_DEBUG_LEVEL');
	if ($debug_level == DEBUG_DISPLAY_ERRORS_SQL) {
		$l = Log::getInstance();
		foreach($l->getQueries() as $lq) {
			print $lq;
		}
	}
	if (is_object($db)) {
		$db->disconnect();
	}

}
exit;