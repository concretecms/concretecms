<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
class DeletePagePageWorkflowRequest extends PageWorkflowRequest {
	
	public function __construct() {
		$pk = PermissionKey::getByHandle('delete_page');
		parent::__construct($pk);
	}
	
}