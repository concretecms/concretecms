<?php
/**
*
* Removes old page versions
* @package Utilities
*/

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Job_RemoveOldPageVersions extends Job {

	public function getJobName() {
		return t("Remove Old Page Versions");
	}
	
	public function getJobDescription() {
		return t("Removes all except the 10 most recent page versions for each page.");
	}
		
	public function run() {
		//$u = new User();
		//if(!$u->isSuperUser()) { die(t("Access Denied."));} // cheap security check...
		$cfg = new Config();
		Loader::model('page_list');
		$pl = new PageList();
		//$pl->ignorePermissions();
		$pNum = $cfg->get('OLD_VERSION_JOB_PAGE_NUM');
		if($pNum <= 0) {
			$cfg->save('OLD_VERSION_JOB_PAGE_NUM',0);
		}
		
		$pl->setItemsPerPage(3);
		
		/* probably want to keep a record of pages that have been gone through 
		 * so you don't start from the beginning each time..
		 */
		$pNum = $pNum +1;
		$pages = $pl->getPage($pNum);
		$cfg->save('OLD_VERSION_JOB_PAGE_NUM',$pNum);
		
		$pageCount = 0;
		$versionCount = 0;
		if(count($pages) == 0) {
			$cfg->save('OLD_VERSION_JOB_PAGE_NUM',0);
			return t("All pages have been processes, starting from beginning on next run.");
		}
		foreach($pages as $page) {
			if($page instanceof Page) {
				$pvl = new VersionList($page);
				$versions = $pvl->getVersionListArray();
				$versions = array_reverse($versions);
				
				$vCount = count($versions);
				if($vCount <= 10) { continue; }
				$pageCount++;
				$stopAt = $vCount - 10;
				$i = 0;
				foreach($versions as $v) {
					if($v instanceof CollectionVersion) {
						if($v->isApproved() || $v->isMostRecent()) { // may want to add a date check here too
							continue;
						} else {
							@$v->delete();
							$versionCount++;
						}
					}
					$i++;
					if($i >= $stopAt) { break; }
				}
			}
		}
		$pages = ($pageCount==1) ? t("Page") : t("Pages");
		return 	$versionCount . " " . t("versions deleted from") . " " . $pageCount . " " . $pages . " (".$pNum.")";
	}
}

?>