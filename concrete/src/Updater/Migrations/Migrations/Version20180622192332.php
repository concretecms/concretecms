<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Job\Job;
use Concrete\Core\Job\Set;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;


class Version20180622192332 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->createSinglePage('/dashboard/system/registration/deactivation', 'User Deactivation Settings');
        $job = Job::getByHandle('deactivate_users');
        if ($job === null) {
            Job::installByHandle('deactivate_users');
            $job = Job::getByHandle('deactivate_users');
            if ($job !== null) {
                $set = Set::getByName('Default');
                if ($set !== null) {
                    $set->addJob($job);
                }
            }
        }


    }


}
