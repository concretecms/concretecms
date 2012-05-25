<?
defined('C5_EXECUTE') or die("Access Denied.");
class BlockTypePermissionKey extends PermissionKey {
		

	/** 
	 * No workflow functionality in blocks
	 * @private
	 */
	public function clearWorkflows() {}
	
	/** 
	 * @private
	 */
	public function attachWorkflow(Workflow $wf) {}
	
	/** 
	 * @private
	 */
	public function getWorkflows() {return array();}
	
}

class BlockTypePermissionAccess extends PermissionAccess {
	
	
}

class BlockTypePermissionAccessListItem extends PermissionAccessListItem {
	
	
}