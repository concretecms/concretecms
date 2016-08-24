<?php
namespace Concrete\Job;

use Concrete\Core\Cache\Cache;
use Concrete\Core\Page\PageList;
use Concrete\Core\Support\Facade\Application;
use Config;
use Job as AbstractJob;
use Core;
use Database;
use PermissionKey;
use Group;
use DateTime;
use CollectionAttributeKey;
use Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use Concrete\Core\Multilingual\Page\Section\Section as MultilingualSection;
use SimpleXMLElement;
use Page;
use Events;

class UpdateStatistics extends AbstractJob
{

    /** Returns the job name.
     * @return string
     */
    public function getJobName()
    {
        return t('Update Statistics Trackers');
    }

    /** Returns the job description.
     * @return string
     */
    public function getJobDescription()
    {
        return t('Scan the sitemap for file usage and stack usage to update statistics trackers');
    }

    /** Executes the job.
     * @throws \Exception Throws an exception in case of errors.
     *
     * @return string Returns a string describing the job result in case of success.
     */
    public function run()
    {
        $pagination = (new PageList())->getPagination();
        $app = Application::getFacadeApplication();
        $tracker = $app->make('statistics/tracker');

        do {
            if ($results = $pagination->getCurrentPageResults()) {
                /** @var \Concrete\Core\Page\Page $page */
                foreach ($results as $page) {
                    try {
                        $tracker->track($page);
                    } catch (\Exception $e) {
                        dd($e->getMessage(), $e->getTraceAsString());
                    }
                }
            }
        } while ($pagination->hasNextPage() && $pagination->setCurrentPage($pagination->getNextPage()));
    }

}
