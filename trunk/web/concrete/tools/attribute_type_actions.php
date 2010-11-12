<?
	defined('C5_EXECUTE') or die("Access Denied.");
	if (isset($_REQUEST['akID'])) {
		$at = AttributeKey::getInstanceByID($_REQUEST['akID']);
	} else {
		$at = AttributeType::getByID($_REQUEST['atID']);
	}
	if (is_object($at)) {
		$cnt = $at->getController();
		if (isset($_REQUEST['args']) && is_array($_REQUEST['args'])) {
			$args = $_REQUEST['args'];
		} else {  
			$args = array(); 
		}
		call_user_func_array(array($cnt, 'action_' . $_REQUEST['action']), $args);
	}