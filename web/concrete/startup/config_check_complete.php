<?
defined('C5_EXECUTE') or die("Access Denied.");
if ($config_check_failed) {
	$r = Request::getInstance();
	if (!$r->matches('/install/*') && $r->getPath() != '/install') {
		Redirect::send('/install');
	}
}