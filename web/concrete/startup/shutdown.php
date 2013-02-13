<?
defined('C5_EXECUTE') or die("Access Denied.");
$db = Loader::db(false, false, false, false, false, false);
if (is_object($db)) {
	$db->disconnect();
}

if (defined('ENABLE_OVERRIDE_CACHE') && ENABLE_OVERRIDE_CACHE) {
	Environment::saveCachedEnvironmentObject();
} else if (defined('ENABLE_OVERRIDE_CACHE') && (!ENABLE_OVERRIDE_CACHE)) {
	$env = Environment::get();
	$env->clearOverrideCache();
}
exit;