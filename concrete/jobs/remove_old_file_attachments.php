<?php

namespace Concrete\Job;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\File;
use Concrete\Core\Support\Facade\Application;
use Job as AbstractJob;

class RemoveOldFileAttachments extends AbstractJob
{
    public function getJobName()
    {
        return t("Remove Old File Attachments");
    }

    public function getJobDescription()
    {
        return t("Removes all expired file attachments from private messages.");
    }

    public function run()
    {


    }
}
