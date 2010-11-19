<?php 
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

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion532Helper {

	protected $numImported = 0;

	protected function incrementImported() {
		$this->numImported++;
		if ($this->numImported > 3000) {
			die("3000 records imported. Please re-run the upgrade script until this message goes away.");
		}
	}
	
	public function prepare($cnt) {
		// Handle new attribute stuff
		$db = Loader::db();
		
		$dict = NewDataDictionary($db->db, DB_TYPE);
		$tables = $db->MetaTables();
		if (!in_array('_UserAttributeKeys', $tables) && in_array('UserAttributeKeys', $tables)) { 		
			$dict->ExecuteSQLArray($dict->RenameTableSQL('UserAttributeKeys', '_UserAttributeKeys'));
		}
		if(!in_array('_CollectionAttributeKeys',$tables) && in_array('CollectionAttributeKeys', $tables)) { 
			$dict->ExecuteSQLArray($dict->RenameTableSQL('CollectionAttributeKeys', '_CollectionAttributeKeys'));
		}
		if(!in_array('_FileAttributeKeys',$tables) && in_array('FileAttributeKeys', $tables)) {
			$dict->ExecuteSQLArray($dict->RenameTableSQL('FileAttributeKeys', '_FileAttributeKeys'));
		}
		if(!in_array('_CollectionAttributeValues', $tables) && in_array('CollectionAttributeValues', $tables)) {
			$dict->ExecuteSQLArray($dict->RenameTableSQL('CollectionAttributeValues', '_CollectionAttributeValues'));
		}
		if(!in_array('_UserAttributeValues', $tables) && in_array('UserAttributeValues', $tables)) {
			$dict->ExecuteSQLArray($dict->RenameTableSQL('UserAttributeValues', '_UserAttributeValues'));
		}
		if(!in_array('_FileAttributeValues', $tables) && in_array('FileAttributeValues', $tables)) {			
			$dict->ExecuteSQLArray($dict->RenameTableSQL('FileAttributeValues', '_FileAttributeValues'));
		}
		if(!in_array('_PageSearchIndexAttributes', $tables) && in_array('PageSearchIndexAttributes', $tables)) {
			$dict->ExecuteSQLArray($dict->RenameTableSQL('PageSearchIndexAttributes', '_PageSearchIndexAttributes'));
		}

		$tables = $db->MetaTables();
		if(in_array('_UserAttributeValues', $tables)) {
			$columns = $db->MetaColumns('_UserAttributeValues');
			if (in_array('_UserAttributeValues', $tables) && !isset($columns['ISIMPORTED'])) {
				$q = $dict->AddColumnSQL('_UserAttributeValues', 'isImported I1 DEFAULT 0 NULL');
				$db->Execute($q[0]);
			}
		}
		
		if(in_array('_FileAttributeValues', $tables)) {
			$columns = $db->MetaColumns('_FileAttributeValues');
			if (in_array('_FileAttributeValues', $tables) && !isset($columns['ISIMPORTED'])) {
				$q = $dict->AddColumnSQL('_FileAttributeValues', 'isImported I1 DEFAULT 0 NULL');
				$db->Execute($q[0]);
			}
		}
		
		if(in_array('_CollectionAttributeValues', $tables)) {
			$columns = $db->MetaColumns('_CollectionAttributeValues');
			if (in_array('_CollectionAttributeValues', $tables) && !isset($columns['ISIMPORTED'])) {
				$q = $dict->AddColumnSQL('_CollectionAttributeValues', 'isImported I1 DEFAULT 0 NULL');
				$db->Execute($q[0]);
			}
		}
		
		//$cnt->upgrade_db = false; // schema refresh allways
		
		$cnt->refresh_schema();// refresh the db schema to match 5.3.3 - moved here so it's not called with each upgrade, just the 5.3.3
	}
	
	public function run() {
		$db = Loader::db();
		
		Cache::disableLocalCache();
		Loader::model('attribute/categories/collection');
		Loader::model('attribute/categories/file');
		Loader::model('attribute/categories/user');
		$collectionErrors = array();
		$fileErrors = array();
		$userErrors = array();
		//add the new collection attribute keys
		$this->installCoreAttributeItems();	
		
		$dict = NewDataDictionary($db->db, DB_TYPE);
		$tables = $db->MetaTables();
		
		if (in_array('_CollectionAttributeKeys', $tables)) {
			$collectionErrors = $this->upgradeCollectionAttributes();
		}		
		if (in_array('_FileAttributeKeys', $tables)) {
			$fileErrors = $this->upgradeFileAttributes();
		}			
		if (in_array('_UserAttributeKeys', $tables)) {
			$userErrors = $this->upgradeUserAttributes();
		} 

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
		
		$em = Page::getByPath('/dashboard/settings');
		if (!$em->isError()) {
			$em = SinglePage::getByID($em->getCollectionID());
			$em->refresh();
		}
		
		$em1=Page::getByPath('/dashboard/settings/mail');
		if ($em1->isError()) {
			$em1 = SinglePage::add('/dashboard/settings/mail');
			$em1->update(array('cName'=>t('Email'), 'cDescription'=>t('Enable post via email and other settings.')));
		}

		// remove adodb database logs
		$databaseReports = Page::getByPath('/dashboard/reports/database');
		if (!$databaseReports->isError()) {
			$databaseReports->delete();
		}
		if (in_array('adodb_logsql', $tables)) {			
			@$db->query('DROP TABLE adodb_logsql');	
		}
		
		Loader::library('mail/importer');
		$mi = MailImporter::getByHandle("private_message");
		if (!is_object($mi)) {
			MailImporter::add(array('miHandle' => 'private_message'));
		}

		Loader::model("job");
		Job::installByHandle('process_email');		

		Cache::enableLocalCache();
	
		return array_merge($collectionErrors, $fileErrors, $userErrors);
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
		$textareat = AttributeType::add('textarea', t('Text Area'));
		$boolt = AttributeType::add('boolean', t('Checkbox'));
		$dtt = AttributeType::add('date_time', t('Date/Time'));
		$ift = AttributeType::add('image_file', t('Image/File'));
		$nt = AttributeType::add('number', t('Number'));
		$rt = AttributeType::add('rating', t('Rating'));
		$st = AttributeType::add('select', t('Select'));
		$addresst = AttributeType::add('address', t('Address'));
		
		// assign collection attributes
		$cakc->associateAttributeKeyType($tt);
		$cakc->associateAttributeKeyType($textareat);
		$cakc->associateAttributeKeyType($boolt);
		$cakc->associateAttributeKeyType($dtt);
		$cakc->associateAttributeKeyType($ift);
		$cakc->associateAttributeKeyType($nt);
		$cakc->associateAttributeKeyType($rt);
		$cakc->associateAttributeKeyType($st);
		
		// assign user attributes
		$uakc->associateAttributeKeyType($tt);
		$uakc->associateAttributeKeyType($textareat);
		$uakc->associateAttributeKeyType($boolt);
		$uakc->associateAttributeKeyType($dtt);
		$uakc->associateAttributeKeyType($nt);
		$uakc->associateAttributeKeyType($st);
		$uakc->associateAttributeKeyType($addresst);
		
		// assign file attributes
		$fakc->associateAttributeKeyType($tt);
		$fakc->associateAttributeKeyType($textareat);
		$fakc->associateAttributeKeyType($boolt);
		$fakc->associateAttributeKeyType($dtt);
		$fakc->associateAttributeKeyType($nt);
		$fakc->associateAttributeKeyType($rt);
		$fakc->associateAttributeKeyType($st);
	}
	
	protected function upgradeCollectionAttributes() {
		$messages = array();
		$db = Loader::db();
		$r = $db->Execute('select _CollectionAttributeKeys.* from _CollectionAttributeKeys order by _CollectionAttributeKeys.akID asc');
		while ($row = $r->FetchRow()) {
			$cleanHandle = preg_replace("/[^A-Za-z0-9\_]/",'',$row['akHandle']); // remove spaces, chars that'll mess up our index tables
			$existingAKID = $db->GetOne('select akID from AttributeKeys where akHandle = ?', array($cleanHandle) );
			if ($existingAKID < 1) {
				$args = array(
					'akHandle' => $cleanHandle, 
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
				try {
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
				} catch (Exception $e) {
					$messages[] = t('Error while converting the attributes on cID: %s Error:<br/>%s', $row2['cID'], $e->getMessage());
				 	continue;
				}
			}
			
			unset($ak);
			unset($row2);
			$r2->Close();
			unset($r2);
		}
		
		unset($row);
		$r->Close();
		unset($r);
	
		return $messages;
	}

	protected function upgradeFileAttributes() {
		$messages = array();
		$db = Loader::db();
		$r = $db->Execute('select _FileAttributeKeys.* from _FileAttributeKeys order by fakID asc');
		while ($row = $r->FetchRow()) {
			$cleanHandle = preg_replace("/[^A-Za-z0-9\_]/",'',$row['akHandle']); // remove spaces, chars that'll mess up our index tables
			$existingAKID = $db->GetOne('select akID from AttributeKeys where akHandle = ?',  array($cleanHandle) );
			if ($existingAKID < 1) {
				$args = array(
					'akHandle' => $cleanHandle,
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
						$sttype = 'SELECT';
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
		return $messages;
	}

	protected function upgradeUserAttributes() {
		$messages = array();
		$db = Loader::db();
		$r = $db->Execute('select _UserAttributeKeys.* from _UserAttributeKeys order by displayOrder asc');
		while ($row = $r->FetchRow()) {
			$cleanHandle = preg_replace("/[^A-Za-z0-9\_]/",'',$row['ukHandle']); // remove spaces, chars that'll mess up our index tables
			$existingAKID = $db->GetOne('select akID from AttributeKeys where akHandle = ?',  array($cleanHandle) );
			if ($existingAKID < 1) {
				if(!$row['ukHandle']) continue; 
				$args = array(
					'akHandle' => $cleanHandle, 
					'akIsSearchable' => 1,
					'akIsEditable' => 1,
					'akName' => $row['ukName'],
					'uakIsActive' => ($row['ukHidden']?0:1),
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
				if(is_object($ui)) {
					$value = $row2['value'];
					$ui->setAttribute($ak, $value);
				}
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
		return $messages;
	}
		
}
		
	