<?
defined('C5_EXECUTE') or die("Access Denied.");

$tp = Loader::helper('concrete/user');
if (!$tp->canAccessUserSearchInterface()) { 
	die(t("You have no access to users."));
}

// this should be cleaned up.... yeah
$db = Loader::db();
// update order of collections
Loader::model('user_attributes');
$uats = $_REQUEST['akID'];
/*for($i = 0; $i < count($uats); $i++) {
	$uats[$i] = substr($uats[$i], 5);
}*/
UserAttributeKey::updateAttributesDisplayOrder($uats);