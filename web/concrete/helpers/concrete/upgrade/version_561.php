<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion561Helper {

	
	public $dbRefreshTables = array(
		'Blocks',
		'CollectionVersionBlocksOutputCache'
	);

	public function run() {
		$sp = Page::getByPath('/dashboard/system/seo/excluded');
		if (!is_object($sp) || $sp->isError()) {
			$sp = SinglePage::add('/dashboard/system/seo/excluded');
			$sp->update(array('cName'=>t('Excluded URL Word List')));
		}
		$bt = BlockType::getByHandle('next_previous');
		if (is_object($bt)) {
			$bt->refresh();
		}
	}

	
}
