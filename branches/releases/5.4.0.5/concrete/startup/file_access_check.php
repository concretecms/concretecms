<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

if ($config_check_failed) {
	if (basename($_SERVER['PHP_SELF']) == DISPATCHER_FILENAME_CORE) {
		die(_("Access Denied."));
	}
}