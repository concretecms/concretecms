<?php 
$db = Loader::db();
if (is_object($db)) {
	$db->disconnect();
}
exit;