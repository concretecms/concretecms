<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\File\Import\Processor\SvgProcessor;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20190310000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $config = $this->app->make('config');
        if (empty($config->get('concrete.file_manager.images.svg_sanitization.action'))) {
            $action = $config->get('concrete.file_manager.images.svg_sanitization.enabled') ? SvgProcessor::ACTION_SANITIZE : SvgProcessor::ACTION_DISABLED;
            $config->set('concrete.file_manager.images.svg_sanitization.action', $action);
            $config->save('concrete.file_manager.images.svg_sanitization.action', $action);
        }
    }
}
