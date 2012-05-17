<?
defined('C5_EXECUTE') or die("Access Denied.");
$db = Loader::db(false, false, false, false, false, false);
if (is_object($db)) {
	$env = Environment::get();
	$env->shutdown();
	$db->disconnect();
}
exit;