<?
$db = Loader::db();
if (is_object($db)) {
	
	$debug_level = Config::get('SITE_DEBUG_LEVEL');
	//$debug_level = DEBUG_DISPLAY_ERRORS_SQL;

	if ($debug_level == DEBUG_DISPLAY_ERRORS_SQL) {
		$l = DBLog::getInstance();
		$queries = $l->getQueries();
		print '<B>TOTAL QUERIES</B>:' . count($queries);
		foreach($l->getQueries() as $lq) {
			print $lq;
		}
	}
	if (is_object($db)) {
		$db->disconnect();
	}

}
exit;