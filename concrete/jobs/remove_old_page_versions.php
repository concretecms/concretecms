<?php
namespace Concrete\Job;

use Concrete\Core\Page\Collection\Version\VersionList;
use Job as AbstractJob;
use Config;
use PageList;
use Concrete\Core\Page\Collection\Version\Version;

class RemoveOldPageVersions extends AbstractJob
{
    public function getJobName()
    {
        return t("Remove Old Page Versions");
    }

    public function getJobDescription()
    {
        return t("Removes all except the 10 most recent page versions for each page.");
    }

    public function run()
    {
        $pNum = (int) Config::get('concrete.maintenance.version_job_page_num');
        $pNum = $pNum < 0 ? 1 : $pNum + 1;

        $pl = new PageList();
        $pl->ignorePermissions();
        $pl->setItemsPerPage(3);
        $pl->filter('p.cID', $pNum, '>=');
        $pl->sortByCollectionIDAscending();
        $pagination = $pl->getPagination();
        $pages = $pagination->getCurrentPageResults();

        /* probably want to keep a record of pages that have been gone through
         * so you don't start from the beginning each time..
         */

        if (!count($pages)) {
            Config::save('concrete.maintenance.version_job_page_num', 0);

            return t("All pages have been processed, starting from beginning on next run.");
        }

        $versionCount = 0;
        $pagesAffected = array();
        foreach ($pages as $page) {
            $pvl = new VersionList($page);
            $pagesAffected[] = $page->getCollectionID();
            foreach (array_slice($pvl->get(), 10) as $v) {
                if ($v instanceof Version && !$v->isApproved() && !$v->isMostRecent()) {
                    @$v->delete();
                    ++$versionCount;
                }
            }
            $pNum = $page->getCollectionID();
        }
        $pageCount = count($pagesAffected);
        Config::save('concrete.maintenance.version_job_page_num', $pNum);

        //i18n: %1$d is the number of versions deleted, %2$d is the number of affected pages, %3$d is the number of times that the Remove Old Page Versions job has been executed.
        return t2(
            '%1$d versions deleted from %2$d page (%3$s)',
            '%1$d versions deleted from %2$d pages (%3$s)',
            $pageCount,
            $versionCount,
            $pageCount,
            implode(',', $pagesAffected)
        );
    }
}
