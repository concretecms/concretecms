<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$db = Loader::db();
if (is_object($db)) {
	$db->disconnect();
}
exit;