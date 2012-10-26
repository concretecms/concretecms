<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion561Helper {

	
	public $dbRefreshTables = array(
		'Blocks',
		'CollectionVersionBlocksOutputCache',
		'PermissionAccessList'
	);

	public function run() {
		$sp = Page::getByPath('/dashboard/system/seo/excluded');
		if (!is_object($sp) || $sp->isError()) {
			$sp = SinglePage::add('/dashboard/system/seo/excluded');
			$sp->update(array('cName'=>t('Excluded URL Word List')));
			$sp->setAttribute('meta_keywords', 'pretty, slug');
		}
		$bt = BlockType::getByHandle('next_previous');
		if (is_object($bt)) {
			$bt->refresh();
		}

		$db = Loader::db();
		$columns = $db->MetaColumns('Pages');
		if (isset($columns['PTID'])) {
			$db->Execute('alter table Pages drop column ptID');
		}

		if (isset($columns['CTID'])) {
			$db->Execute('alter table Pages drop column ctID');
		}

		$bt = BlockType::getByHandle('search');
		if (is_object($bt)) {
			$bt->refresh();
		}
	}

	
}
