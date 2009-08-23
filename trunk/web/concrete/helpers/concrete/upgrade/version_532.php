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

	protected $numImported = 0;

	protected function incrementImported() {
		$this->numImported++;
		if ($this->numImported > 3000) {
			die("3000 records imported. Please re-run the upgrade script until this message goes away.");
		}
	}
	
	//run before the db.xml changes take place
	public function prepare($cnt) {
		// Handle new attribute stuff
		$db = Loader::db();
		$dict = NewDataDictionary($db->db, DB_TYPE);
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

		$columns = $db->MetaColumns('_UserAttributeValues');
		if (!isset($columns['ISIMPORTED'])) {
			$q = $dict->AddColumnSQL('_UserAttributeValues', 'isImported I1 DEFAULT 0 NULL');
			$db->Execute($q[0]);
			$q = $dict->AddColumnSQL('_FileAttributeValues', 'isImported I1 DEFAULT 0 NULL');
			$db->Execute($q[0]);
			$q = $dict->AddColumnSQL('_CollectionAttributeValues', 'isImported I1 DEFAULT 0 NULL');
			$db->Execute($q[0]);
		} else {
			$cnt->upgrade_db = false;
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


		$membersPage =Page::getByPath('/members');
		if( !intval($membersPage->cID)) {
			SinglePage::add('/members');
		}

		$messagesPage =Page::getByPath('/profile/messages');
		if( !intval($messagesPage->cID)) {
			SinglePage::add('/profile/messages');
		}
		
		$ppme = UserAttributeKey::getByHandle('profile_private_messages_enabled');
		if (!is_object($ppme)) {
			UserAttributeKey::add('BOOLEAN', array('akHandle' => 'profile_private_messages_enabled', 'akName' => t('I would like to receive private messages.'), 'akIsSearchable' => true));
		}
		$ppmne = UserAttributeKey::getByHandle('profile_private_messages_notification_enabled');
		if (!is_object($ppmne)) {
			UserAttributeKey::add('BOOLEAN', array('akHandle' => 'profile_private_messages_notification_enabled', 'akName' => t('Send me email notifications when I receive a private message.'), 'akIsSearchable' => true));
		}

		$em1=Page::getByPath('/dashboard/mail');
		$em2=Page::getByPath('/dashboard/mail/importers');
		if ($em1->isError()) {
			$em1 = SinglePage::add('/dashboard/mail');
			$em1->update(array('cName'=>t('Mail Setup'), 'cDescription'=>t('Enable post via email and other settings.')));
		}
		
		if ($em2->isError()) {
			$em2 = SinglePage::add('/dashboard/mail/importers');
		}

		Loader::library('mail/importer');
		$mi = MailImporter::getByHandle("private_message");
		if (!is_object($mi)) {
			MailImporter::add(array('miHandle' => 'private_message'));
		}

		Loader::model("job");
		Job::installByHandle('process_email');		

		Cache::enableLocalCache();
	}
	
	protected function installCoreAttributeItems() {
		$cakc = AttributeKeyCategory::getByHandle('collection');
		if (is_object($cakc)) {
			return false;
		}
		
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
		$r = $db->Execute('select _CollectionAttributeKeys.* from _CollectionAttributeKeys order by _CollectionAttributeKeys.akID asc');
		while ($row = $r->FetchRow()) {
			$existingAKID = $db->GetOne('select akID from AttributeKeys where akHandle = ?', $row['akHandle']);
			if ($existingAKID < 1) {
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
			} else {
				$ak = CollectionAttributeKey::getByID($existingAKID);
			}
			
			$r2 = $db->Execute('select * from _CollectionAttributeValues where akID = ? and isImported = 0', $row['akID']);
			while ($row2 = $r2->FetchRow()) {
				$nc = Page::getByID($row2['cID'], $row2['cvID']);
				$value = $row2['value'];
				if ($row['akType'] == 'SELECT' || $row['akType'] == 'SELECT_MULTIPLE') {
					$value = explode("\n", $value);
					$nc->setAttribute($ak, $value);
				} else if ($row['akType'] == 'IMAGE_FILE') {
					$value = File::getByID($value);
					if (is_object($value) && $value->getFileID() > 0) {
						$nc->setAttribute($ak, $value);
					}
				} else {
					$nc->setAttribute($ak, $value);
				}				
				unset($nc);
				$db->Execute('update _CollectionAttributeValues set isImported = 1 where akID = ? and cvID = ? and cID = ?', array($row['akID'], $row2['cvID'], $row2['cID']));
				$this->incrementImported();
			}
			
			unset($ak);
			unset($row2);
			$r2->Close();
			unset($r2);
		}
		
		unset($row);
		$r->Close();
		unset($r);
	}

	protected function upgradeFileAttributes() {
		Loader::model('attribute/categories/file');
		$db = Loader::db();
		$r = $db->Execute('select _FileAttributeKeys.* from _FileAttributeKeys order by fakID asc');
		while ($row = $r->FetchRow()) {
			$existingAKID = $db->GetOne('select akID from AttributeKeys where akHandle = ?', $row['akHandle']);
			if ($existingAKID < 1) {
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
					case 'SELECT_ADD':
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
			} else {
				$ak = FileAttributeKey::getByID($existingAKID);
			}
			
			$r2 = $db->Execute('select * from _FileAttributeValues where fakID = ? and isImported = 0', $row['fakID']);
			while ($row2 = $r2->FetchRow()) {
				$f = File::getByID($row2['fID']);
				$fv = $f->getVersion($row2['fvID']);
				$value = $row2['value'];
				if ($row['akType'] == 'SELECT' || $row['akType'] == 'SELECT_MULTIPLE' || $row['akType'] == 'SELECT_ADD') {
					$value = explode("\n", $value);					
				}
				$fv->setAttribute($ak, $value);
				unset($f);
				unset($fv);

				$db->Execute('update _FileAttributeValues set isImported = 1 where fakID = ? and fvID = ? and fID = ?', array($row['fakID'], $row2['fvID'], $row2['fID']));
				$this->incrementImported();
			}
			
			unset($ak);
			unset($row2);
			$r2->Close();
			unset($r2);
		}
		
		unset($row);
		$r->Close();
		unset($r);
	}

	protected function upgradeUserAttributes() {
		Loader::model('attribute/categories/user');
		$db = Loader::db();
		$r = $db->Execute('select _UserAttributeKeys.* from _UserAttributeKeys order by displayOrder asc');
		while ($row = $r->FetchRow()) {
			$existingAKID = $db->GetOne('select akID from AttributeKeys where akHandle = ?', $row['ukHandle']);
			if ($existingAKID < 1) {
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
			} else {
				$ak = UserAttributeKey::getByID($existingAKID);
			}
			
			$r2 = $db->Execute('select * from _UserAttributeValues where ukID = ? and isImported = 0', $row['ukID']);
			while ($row2 = $r2->FetchRow()) {
				$ui = UserInfo::getByID($row2['uID']);
				$value = $row2['value'];
				$ui->setAttribute($ak, $value);
				unset($ui);
				
				$db->Execute('update _UserAttributeValues set isImported = 1 where ukID = ? and uID = ?', array($row['ukID'], $row2['uID']));
				$this->incrementImported();

			}
			
			unset($ak);
			unset($row2);
			$r2->Close();
			unset($r2);
		}
		
		unset($row);
		$r->Close();
		unset($r);
	}
		
}
		
	