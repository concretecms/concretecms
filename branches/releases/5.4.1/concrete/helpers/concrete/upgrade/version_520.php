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
class ConcreteUpgradeVersion520Helper {
	
	public function prepare() {
		$db = Loader::db();
		$columns = $db->MetaColumns('PagePaths');
		if ($columns['PPID'] == false) {

			$db->Execute('alter table PagePaths change cID ppID int unsigned not null auto_increment');
			$db->Execute('alter table PagePaths add column cID int unsigned not null default 0');
			$db->Execute('update PagePaths set cID = ppID');

		}
		
		$columns = $db->MetaColumns('btFormQuestions');
		if ($columns['QID'] == false) {
			try{
				$db->query('ALTER TABLE btFormQuestions CHANGE msqID msqID INT(11) UNSIGNED NOT NULL '); 
				$db->query('ALTER TABLE btFormQuestions DROP PRIMARY KEY'); 
			}catch(Exception $e){ }
			try{
				$db->query('ALTER TABLE btFormQuestions ADD qID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY '); 			
			}catch(Exception $e){ } 

		}

		$columns = $db->MetaColumns('btSlideshowImg');
		if ($columns['FID'] == false && $columns['IMAGE_BID'] != false) {
			try{
				$db->query('ALTER TABLE btSlideshowImg CHANGE image_bID fID INT(11) unsigned null'); 
			}catch(Exception $e){ } 
		}
	}
	
	public function run() {
		$db = Loader::db();
		$tables = $db->MetaTables('TABLES');

		if (in_array('btFormQuestions', $tables)) {
			
			//$db->CacheFlush();
			//$db->setDebug(true);

			$questionsWithBIDs=$db->getAll('SELECT max(bID) AS bID, btForm.questionSetId AS qSetId FROM `btForm` GROUP BY questionSetId');
			foreach($questionsWithBIDs as $questionsWithBID){
				$vals=array( intval($questionsWithBID['bID']), intval($questionsWithBID['qSetId']) );
				$rs=$db->query('UPDATE btFormQuestions SET bID=? WHERE questionSetId=? AND bID=0',$vals);  
			}
		}
		
		// now we populate files
		$num = $db->GetOne("select count(*) from Files");
		if ($num < 1) {
			$r = $db->Execute("select btFile.*, Blocks.bDateAdded from btFile inner join Blocks on btFile.bID = Blocks.bID");
			while ($row = $r->fetchRow()) {
				$v = array($row['bID'], 1, $row['filename'], null, '', $row['origfilename'], $row['bDateAdded']);
				$db->Execute("insert into FileVersions (fID, fvID, fvFilename, fvPrefix, fvExtension, fvTitle, fvDateAdded) values (?, ?, ?, ?, ?, ?, ?)", $v);	
				$db->Execute("insert into Files (fID, fDateAdded) values (?, ?)", array($row['bID'], $row['bDateAdded']));
			}
		}

		Loader::model('single_page');
		// Rename Forms to Reports
		$p = Page::getByPath('/dashboard/mediabrowser');
		if (!$p->isError()) {
			$p->delete();
		}

		$p = Page::getByPath('/dashboard/files');
		if ($p->isError()) {
			$d2 = SinglePage::add('/dashboard/files');
			$d2a = SinglePage::add('/dashboard/files/search');
			$d2b = SinglePage::add('/dashboard/files/attributes');
			$d2c = SinglePage::add('/dashboard/files/sets');
			$d2d = SinglePage::add('/dashboard/files/access');						
			$d2->update(array('cName'=>t('File Manager'), 'cDescription'=>t('All documents and images.')));
			$d3b = SinglePage::add('/dashboard/reports/surveys');
		}

		$p = Page::getByPath('/dashboard/scrapbook');
		if ($p->isError()) {
			$d3 = SinglePage::add('/dashboard/scrapbook');
			$d3b = SinglePage::add('/dashboard/scrapbook/user');
			$d3a = SinglePage::add('/dashboard/scrapbook/global');
			$d3->update(array('cName'=>t('Scrapbook'), 'cDescription'=>t('Share content across your site.')));
		}
			
		Loader::model('file_set');
		Loader::model('groups');
		
		$htbt = BlockType::getByHandle('html');
		if (!is_object($htbt)) {
			BlockType::installBlockType('html');			
		}
		
		$g1 = Group::getByID(GUEST_GROUP_ID);
		$g2 = Group::getByID(REGISTERED_GROUP_ID);
		$g3 = Group::getByID(ADMIN_GROUP_ID);
		
		$fs = FileSet::getGlobal();
		$fs->setPermissions($g1, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_NONE);
		$fs->setPermissions($g2, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_NONE);
		$fs->setPermissions($g3, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_ALL);


		$p = Page::getByPath('/dashboard/reports/surveys');
		if ($p->isError()) {
			$p = SinglePage::add('/dashboard/reports/surveys');
		}
	}
	
}
		
	