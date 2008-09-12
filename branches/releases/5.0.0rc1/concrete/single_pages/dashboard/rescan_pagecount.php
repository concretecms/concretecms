<?php 

function admin_children_rebuild($cID) {
	$db = Loader::db();
	$children_array = array();
	$c = Page::getByID($cID);

	$children_array = $c->getCollectionChildrenArray();
	// store count($children_array) in cParent's cChildren
	$current_count = count($children_array);
	print "Rebuilding children for: " . $cID . '(' . $current_count . ' found)<br/>';

	$q = "update Pages set cChildren='$current_count' where cID='$cID'";
	$r = $db->query($q);
	foreach($children_array as $newcID) {
		admin_children_rebuild($newcID);
	}
}

?>

<h1>Rescan Page Count</h1>

<?php 
admin_children_rebuild(1);
?>