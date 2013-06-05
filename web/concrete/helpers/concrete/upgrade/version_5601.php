<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion5601Helper {

	
	public $dbRefreshTables = array(
		'Users'
	);
	
	
	public function run() {

		$sp = Page::getByPath('/dashboard/users/group_sets');
		if (is_object($sp) && (!$sp->isError())) {
			$sp->setAttribute('exclude_nav', 0);
		}

	}
		
}
