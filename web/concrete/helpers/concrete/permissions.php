<?
/**
 * @package Helpers
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */ 

defined('C5_EXECUTE') or die("Access Denied."); 

class ConcretePermissionsHelper {  
	
	public function printAddAccessEntityButton() {
		$html = '<a href="' . REL_DIR_FILES_TOOLS_REQUIRED . '/permissions/access_entity" class="btn small dialog-launch" dialog-width="500" dialog-height="500" dialog-title="' . t('Add Access Entity') . '">' . t('Add') . '</a>';
		return $html;
	}


}

?>