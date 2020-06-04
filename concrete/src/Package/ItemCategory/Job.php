<?php

namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;

defined('C5_EXECUTE') or die('Access Denied.');

class Job extends AbstractCategory
{
    public function getItemCategoryDisplayName()
    {
        return t('Jobs');
    }

    public function getItemName($job)
    {
        return $job->getJobName();
    }

    public function getPackageItems(Package $package)
    {
        return \Concrete\Core\Job\Job::getListByPackage($package);
    }
}
