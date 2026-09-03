<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\File\Import\Processor\XmlProcessor;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20260903000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $config = $this->app->make('config');
        if (empty($config->get('concrete.file_manager.documents.xml_sanitization.action'))) {
            $action = XmlProcessor::ACTION_DEFAULT;
            $config->save('concrete.file_manager.documents.xml_sanitization.action', $action);
        }
    }
}