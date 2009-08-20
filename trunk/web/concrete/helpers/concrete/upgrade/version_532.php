<?
/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die(_("Access Denied."));
class ConcreteUpgradeVersion532Helper {

	//run before the db.xml changes take place
	public function prepare() {
		// Handle new attribute stuff
		$db = Loader::db();
		$dict = NewDataDictionary($db->db);
		$tables = $db->MetaTables();
		if (!in_array('_UserAttributeKeys', $tables)) {
			$dict->ExecuteSQLArray($dict->RenameTableSQL('UserAttributeKeys', '_UserAttributeKeys'));
			$dict->ExecuteSQLArray($dict->RenameTableSQL('CollectionAttributeKeys', '_CollectionAttributeKeys'));
			$dict->ExecuteSQLArray($dict->RenameTableSQL('FileAttributeKeys', '_FileAttributeKeys'));
			$dict->ExecuteSQLArray($dict->RenameTableSQL('CollectionAttributeValues', '_CollectionAttributeValues'));
			$dict->ExecuteSQLArray($dict->RenameTableSQL('UserAttributeValues', '_UserAttributeValues'));
			$dict->ExecuteSQLArray($dict->RenameTableSQL('FileAttributeValues', '_FileAttributeValues'));
			$dict->ExecuteSQLArray($dict->RenameTableSQL('PageSearchIndexAttributes', '_PageSearchIndexAttributes'));
		}
	}
	
	public function run() {
		Cache::disableLocalCache();
		
		Loader::model('collection_attributes');
		//add the new collection attribute keys
		$this->installCoreAttributeItems();
		$this->upgradeCollectionAttributes();
		$this->upgradeFileAttributes();
		$this->upgradeUserAttributes();

		
		$cak=CollectionAttributeKey::getByHandle('exclude_sitemapxml');
		if (!is_object($cak)) {
			$cak = CollectionAttributeKey::add('exclude_sitemapxml', t('Exclude From sitemap.xml'), true, null, 'BOOLEAN');
		}
		
		//change the page/tab name of the dashboard users registration page
		$dashboardRegistrationPage=Page::getByPath('/dashboard/users/registration');
		if( intval($dashboardRegistrationPage->cID) ) 
			$dashboardRegistrationPage->update(array('cName'=>t('Login & Registration')));
		Config::save('LOGIN_ADMIN_TO_DASHBOARD', 1);
	
		//profile friends page install	
		Loader::model('single_page');
		$friendsPage=Page::getByPath('/profile/friends');
		if( !intval($friendsPage->cID)) {
			SinglePage::add('/profile/friends');
		}
		
		Cache::enableLocalCache();
	}
	
	protected function installCoreAttributeItems() {
		$cakc = AttributeKeyCategory::add('collection');
		$uakc = AttributeKeyCategory::add('user');
		$fakc = AttributeKeyCategory::add('file');
		
		$tt = AttributeType::add('text', t('Text'));
		$boolt = AttributeType::add('boolean', t('Checkbox'));
		$dtt = AttributeType::add('date_time', t('Date/Time'));
		$ift = AttributeType::add('image_file', t('Image/File'));
		$nt = AttributeType::add('number', t('Number'));
		$rt = AttributeType::add('rating', t('Rating'));
		$st = AttributeType::add('select', t('Select'));
		
		// assign collection attributes
		$cakc->associateAttributeKeyType($tt);
		$cakc->associateAttributeKeyType($boolt);
		$cakc->associateAttributeKeyType($dtt);
		$cakc->associateAttributeKeyType($ift);
		$cakc->associateAttributeKeyType($nt);
		$cakc->associateAttributeKeyType($rt);
		$cakc->associateAttributeKeyType($st);
		
		// assign user attributes
		$uakc->associateAttributeKeyType($tt);
		$uakc->associateAttributeKeyType($boolt);
		$uakc->associateAttributeKeyType($dtt);
		$uakc->associateAttributeKeyType($nt);
		$uakc->associateAttributeKeyType($st);
		
		// assign file attributes
		$fakc->associateAttributeKeyType($tt);
		$fakc->associateAttributeKeyType($boolt);
		$fakc->associateAttributeKeyType($dtt);
		$fakc->associateAttributeKeyType($nt);
		$fakc->associateAttributeKeyType($rt);
		$fakc->associateAttributeKeyType($st);
	}
	
	protected function upgradeCollectionAttributes() {
		Loader::model('attribute/categories/collection');
		$db = Loader::db();
		$r = $db->Execute('select * from _CollectionAttributeKeys order by akID asc');
		while ($row = $r->FetchRow()) {
			$args = array(
				'akHandle' => $row['akHandle'], 
				'akIsSearchable' => $row['akSearchable'],
				'akName' => $row['akName']			
			);
			$sttype = $row['akType'];
			switch($row['akType']) {
				case 'SELECT':
					if ($row['akAllowOtherValues']) {
						$args['akSelectAllowMultipleValues'] = 1;
					}
					break;
				case 'SELECT_MULTIPLE':
					$sttype = 'SELECT';
					$args['akSelectAllowMultipleValues'] = 1;
					if ($row['akAllowOtherValues']) {
						$args['akSelectAllowMultipleValues'] = 1;
					}
					break;
			}
			
			$type = AttributeType::getByHandle(strtolower($sttype));
			$ak = CollectionAttributeKey::add($type, $args);
			if ($sttype == 'SELECT') {
				$selectOptions = explode("\n", $row['akValues']);
				foreach($selectOptions as $so) {
					if ($so != '') {
						SelectAttributeTypeOption::add($ak, $so);
					}
				}
			}
			
			$r2 = $db->Execute('select * from _CollectionAttributeValues where akID = ?', $row['akID']);
			while ($row2 = $r2->FetchRow()) {
				$nc = Page::getByID($row2['cID'], $row2['cvID']);
				$value = $row2['value'];
				if ($row['akType'] == 'SELECT' || $row['akType'] == 'SELECT_MULTIPLE') {
					$value = explode("\n", $value);					
				} else if ($row['akType'] == 'IMAGE_FILE') {
					$value = File::getByID($value);
				}
				$nc->setAttribute($ak, $value);
			}
		}
	}

	protected function upgradeFileAttributes() {
		Loader::model('attribute/categories/file');
		$db = Loader::db();
		$r = $db->Execute('select * from _FileAttributeKeys order by fakID asc');
		while ($row = $r->FetchRow()) {
			$args = array(
				'akHandle' => $row['akHandle'], 
				'akIsSearchable' => $row['akSearchable'],
				'akIsAutoCreated' => $row['akIsImporterAttribute'],
				'akIsEditable' => $row['akIsEditable'],
				'akName' => $row['akName']			
			);
			$sttype = $row['akType'];
			switch($row['akType']) {
				case 'SELECT':
					if ($row['akAllowOtherValues']) {
						$args['akSelectAllowMultipleValues'] = 1;
					}
					break;
				case 'SELECT_MULTIPLE':
					$sttype = 'SELECT';
					$args['akSelectAllowMultipleValues'] = 1;
					if ($row['akAllowOtherValues']) {
						$args['akSelectAllowMultipleValues'] = 1;
					}
					break;
			}
			
			$type = AttributeType::getByHandle(strtolower($sttype));
			$ak = FileAttributeKey::add($type, $args);
			if ($sttype == 'SELECT') {
				$selectOptions = explode("\n", $row['akValues']);
				foreach($selectOptions as $so) {
					if ($so != '') {
						SelectAttributeTypeOption::add($ak, $so);
					}
				}
			}
			
			$r2 = $db->Execute('select * from _FileAttributeValues where fakID = ?', $row['fakID']);
			while ($row2 = $r2->FetchRow()) {
				$f = File::getByID($row2['fID']);
				$fv = $f->getVersion($row2['fvID']);
				$value = $row2['value'];
				if ($row['akType'] == 'SELECT' || $row['akType'] == 'SELECT_MULTIPLE') {
					$value = explode("\n", $value);					
				}
				$fv->setAttribute($ak, $value);
			}
		}
	}

	protected function upgradeUserAttributes() {
		Loader::model('attribute/categories/user');
		$db = Loader::db();
		$r = $db->Execute('select * from _UserAttributeKeys order by displayOrder asc');
		while ($row = $r->FetchRow()) {
			$args = array(
				'akHandle' => $row['ukHandle'], 
				'akIsSearchable' => 1,
				'akIsEditable' => 1,
				'akName' => $row['ukName'],
				'uakIsActive' => $row['ukHidden'],
				'uakProfileEditRequired' => $row['ukRequired'],
				'uakProfileDisplay' => ($row['ukPrivate'] == 0),
				'uakRegisterEdit' => $row['ukDisplayedOnRegister']
			);
			$sttype = $row['ukType'];
			if ($sttype == 'TEXTAREA') {
				$sttype = 'TEXT';
			}
			if ($sttype == 'RADIO') {
				$sttype = 'SELECT';
			}
			$type = AttributeType::getByHandle(strtolower($sttype));
			$ak = UserAttributeKey::add($type, $args);
			if ($sttype == 'SELECT') {
				$selectOptions = explode("\n", $row['ukValues']);
				foreach($selectOptions as $so) {
					if ($so != '') {
						SelectAttributeTypeOption::add($ak, $so);
					}
				}
			}
			
			$r2 = $db->Execute('select * from _UserAttributeValues where ukID = ?', $row['ukID']);
			while ($row2 = $r2->FetchRow()) {
				$ui = UserInfo::getByID($row2['uID']);
				$value = $row2['value'];
				$ui->setAttribute($ak, $value);
			}
		}
	}
		
}
		
	