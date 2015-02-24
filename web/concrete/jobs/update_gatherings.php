<?php
namespace Concrete\Job;

use \Job as AbstractJob;
use \Concrete\Core\Gathering\Gathering;

class UpdateGatherings extends AbstractJob
{

    public function getJobName()
    {
        return t("Update Gatherings");
    }

    public function getJobDescription()
    {
        return t("Loads new items into gatherings.");
    }

    public function run()
    {
        // retrieve all gatherings
        $list = Gathering::getList();
        foreach ($list as $gathering) {
            // generate all new items since the last time the gathering was updated.
            $gathering->generateGatheringItems();
        }
    }
}
