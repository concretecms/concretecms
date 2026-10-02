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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\ItemCategory\AbstractCategory::getPackageItems()
     *
     * @return \Concrete\Core\Job\Job[]
     */
    public function getPackageItems(Package $package)
    {
        return \Concrete\Core\Job\Job::getListByPackage($package);
    }
}
