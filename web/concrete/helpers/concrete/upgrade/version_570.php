<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion570Helper {
	
	public $dbRefreshTables = array(
		
	);
	
	
	public function run() {
		$sp = Page::getByPath('/dashboard/system/seo/excluded');
		if ($sp->isError()) {
			$sp = SinglePage::add('/dashboard/system/seo/excluded');
			$sp->update(array('cName'=>t('Excluded URL Words')));
		}
	}

}
