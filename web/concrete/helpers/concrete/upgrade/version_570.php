<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion570Helper {
	
	public $dbRefreshTables = array(
		'atSocialLinks',
		'atTextareaSettings',
		'UserPointActions',
		'UserPointHistory',
		'Groups',
		'QueuePageDuplicationRelations',
		/*'Queues',*/
		'BlockTypes',
		/*'QueueMessages',*/
		'JobSets',
		'JobSetJobs',
		'SystemContentEditorSnippets'
	);
	
	
	public function run() {
		$bt = BlockType::getByHandle('content');
		if (is_object($bt)) {
			$bt->refresh();
		}

		$tt = AttributeType::getByHandle('social_links');
		if (!is_object($tt) || $tt->getAttributeTypeID() == 0) {
			$tt = AttributeType::add('social_links', t('Social Link'));
		}
		$akc = AttributeKeyCategory::getByHandle('user');
		if (is_object($akc)) {
			$akc->associateAttributeKeyType($tt);
		}

		$js = JobSet::getByName('Default');
		if (!is_object($js)) {
			$js = JobSet::add('Default');
		}
		$js->clearJobs();
		$jobs = Job::getList();
		foreach($jobs as $j) {
			if (!$j->supportsQueue()) {
				$js->addJob($j);	
			}
		}

		$action = UserPointAction::getByHandle('won_badge');
		if (!is_object($action)) {
			UserPointAction::add('won_badge', t('Won a Badge'), 5, false, true);
		}

		$j = Job::getByHandle('index_search_all');
		if (!is_object($j)) {
			Job::installByHandle('index_search_all');
		}

		$j = Job::getByHandle('check_automated_groups');
		if (!is_object($j)) {
			Job::installByHandle('check_automated_groups');
		}

		$sp = Page::getByPath('/dashboard/users/points');
		if ($sp->isError()) {
			$sp = SinglePage::add('/dashboard/users/points');
			$sp->update(array('cName'=>t('Community Points')));
			$sp->setAttribute('icon_dashboard', 'icon-heart');
		}

		$sp = Page::getByPath('/dashboard/users/points/assign');
		if ($sp->isError()) {
			$sp = SinglePage::add('/dashboard/users/points/assign');
			$sp->update(array('cName'=>t('Assign Points')));
		}

		$sp = Page::getByPath('/dashboard/users/points/actions');
		if ($sp->isError()) {
			$sp = SinglePage::add('/dashboard/users/points/actions');
		}

		// Install default control_sets
		Loader::model('system/image_editor/control_set');

		$position = Concrete5_Model_System_ImageEditor_ControlSet::getByHandle('position');
		if ($position->getImageEditorControlSetHandle() != 'position')  {
			Concrete5_Model_System_ImageEditor_ControlSet::add('position','Position');
		}

		$size = Concrete5_Model_System_ImageEditor_ControlSet::getByHandle('size');
		if ($size->getImageEditorControlSetHandle() != 'size')  {
			Concrete5_Model_System_ImageEditor_ControlSet::add('size','Size');
		}

		$this->upgradeRichTextEditor();

	}

	protected function upgradeRichTextEditor() {

		$sns = SystemContentEditorSnippet::getByHandle('page_name');
		if (!is_object($sns)) {
			$sns = SystemContentEditorSnippet::add('page_name', t('Page Name'));
			$sns->activate();
		}
		$sns = SystemContentEditorSnippet::getByHandle('user_name');
		if (!is_object($sns)) {
			$sns = SystemContentEditorSnippet::add('user_name', t('User Name'));
			$sns->activate();
		}

		$db = Loader::db();
		$r = $db->Execute('select * from atTextareaSettings order by akID asc');
		while ($row = $r->FetchRow()) {
			if ($row['akTextareaDisplayMode'] == 'text' || $row['akTextareaDisplayMode'] == 'rich_text_custom' || $row['akTextareaDisplayMode'] == 'rich_text' || $row['akTextareaDisplayMode'] == '') {
				continue;
			}
			$options = array();
			if ($row['akTextareaDisplayMode'] == 'rich_text_basic') {
				$options[] = 'character_styles';
				$options = serialize($options);
				$db->Execute("update atTextareaSettings set akTextareaDisplayMode = 'rich_text_custom', akTextareaDisplayModeCustomOptions = ? where akID = ?", array($row['akID'], $options));
			} else {
				// we just set these all to the default rich text editor mode
				$db->Execute("update atTextareaSettings set akTextareaDisplayMode = 'rich_text' where akID = ?", array($row['akID']));
			}
		}

	}
		
}
