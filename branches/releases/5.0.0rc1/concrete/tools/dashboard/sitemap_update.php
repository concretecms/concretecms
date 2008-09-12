<?php 
$db = Loader::db();
if (isset($_REQUEST['cID']) && is_array($_REQUEST['cID'])) {
	foreach($_REQUEST['cID'] as $displayOrder => $cID) {
		$v = array($displayOrder, $cID);
		$db->query("update Pages set cDisplayOrder = ? where cID = ?", $v);
		
	}
}

$json['error'] = false;
$json['message'] = "Display order saved.";
print json_encode($json);

?>