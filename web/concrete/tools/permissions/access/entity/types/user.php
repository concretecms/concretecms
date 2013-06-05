<?
defined('C5_EXECUTE') or die("Access Denied.");
if (Loader::helper('validation/token')->validate('process')) {
	
	$js = Loader::helper('json');
	$obj = new stdClass;
	$ui = UserInfo::getByID($_REQUEST['uID']);
	if (is_object($ui)) { 
		$pae = UserPermissionAccessEntity::getOrCreate($ui);
		$obj->peID = $pae->getAccessEntityID();
		$obj->label = $pae->getAccessEntityLabel();
	}
	print $js->encode($obj);	
}