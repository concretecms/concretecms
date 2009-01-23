<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

$resp = array();
$valt = Loader::helper('validation/token');
if ($valt->validate('delete_file')) {
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
				$resp['message'] = t('Could not find block.');
			}
		} else {
			$resp['error'] = 1;
			$resp['message'] = t('You do not have permission to remove that block.');
		}
	}
} else {
	$resp['error'] = 1;
	$resp['message'] = $valt->getErrorMessage();
}
$js = Loader::helper('json');
print $js->encode($resp);
?>