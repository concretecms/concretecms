<?php 
$resp = array();
if (isset($_REQUEST['bID'])) {
	$c = Page::getByPath('/dashboard/mediabrowser');
	$cp = new Permissions($c);
	if ($cp->canRead()) {
		$b = Block::getByID($_REQUEST['bID']);
		if (is_object($b)) {
			$b->delete();
			$resp['error'] = 0;
		} else {
			$resp['error'] = 1;
			$resp['message'] = 'Could not find block.';
		}
	} else {
		$resp['error'] = 1;
		$resp['message'] = 'You do not have permission to remove that block.';
	}
}

print json_encode($resp);
?>