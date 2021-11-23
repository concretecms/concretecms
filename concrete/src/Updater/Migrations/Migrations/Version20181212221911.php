<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Authentication\AuthenticationType;
use Exception;

class Version20181212221911 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        /* Add auth types ("handle|name") "twitter|Twitter" and "community|concrete5.org" */
        try {
            $external = AuthenticationType::getByHandle('external_concrete');
        } catch (Exception $e) {
            // AuthenticationType::getByHandle throws an exception if the call fails, which is stupid, but here
            // we are.
            $external = AuthenticationType::add('external_concrete', 'External Concrete');
            if (is_object($external)) {
                $external->disable();
            }
        }
    }
}
