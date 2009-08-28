<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$ch = Page::getByPath("/dashboard/users");
$chp = new Permissions($ch);
if (!$chp->canRead()) {
	die(_("Access Denied."));
}

// this should be cleaned up.... yeah
$db = Loader::db();
// update order of collections
Loader::model('user_attributes');
$uats = $_REQUEST['item'];
/*for($i = 0; $i < count($uats); $i++) {
	$uats[$i] = substr($uats[$i], 5);
}*/
UserAttributeKey::updateAttributesDisplayOrder($uats);