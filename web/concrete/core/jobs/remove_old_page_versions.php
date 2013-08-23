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

		$cfg = new Config;
		$pNum = (int) $cfg->get('OLD_VERSION_JOB_PAGE_NUM');
		$pNum = $pNum < 0 ? 1 : $pNum + 1;
		
		$pl = new PageList;
		$pl->setItemsPerPage(3);
		/* probably want to keep a record of pages that have been gone through 
		 * so you don't start from the beginning each time..
		 */
		$pages = $pl->getPage($pNum);
		
		if(!count($pages)) {
			$cfg->save('OLD_VERSION_JOB_PAGE_NUM',0);
			return t("All pages have been processed, starting from beginning on next run.");
		}

		$versionCount = 0;
		$pagesAffected = array();
		foreach($pages as $page) {
			if($page instanceof Page) {
				$pvl = new VersionList($page);
				$pagesAffected[] = $page->getCollectionID();
				foreach(array_slice(array_reverse($pvl->getVersionListArray()), 10) as $v) {
					if($v instanceof CollectionVersion && !$v->isApproved() && !$v->isMostRecent() ) {
						@$v->delete();
						$versionCount++;
					}
				}
			}
		}
		$pageCount = count($pagesAffected);
		$cfg->save('OLD_VERSION_JOB_PAGE_NUM', $pNum);

		//i18n: %1$d is the number of versions deleted, %2$d is the number of affected pages, %3$d is the number of times that the Remove Old Page Versions job has been executed.
		return t2('%1$d versions deleted from %2$d page (%3$s)', '%1$d versions deleted from %2$d pages (%3$s)', $pageCount, $versionCount, $pageCount, implode(',', $pagesAffected));

	}
}
