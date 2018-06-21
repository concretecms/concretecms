<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20180621000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $config = $this->app->make('config');
        if ($config->get('concrete.user.username.allow_spaces') && !$config->get('concrete.user.username.allow_spaces_migrated')) {
            $rxMiddle = $config->get('concrete.user.username.allowed_characters.middle');
            if (strpos($rxMiddle, ' ') === false) {
                $rxMiddle .= ' ';
                $config->set('concrete.user.username.allowed_characters.middle', $rxMiddle);
                $config->save('concrete.user.username.allowed_characters.middle', $rxMiddle);
            }
            $config->set('concrete.user.username.allow_spaces_migrated', true);
            $config->save('concrete.user.username.allow_spaces_migrated', true);
        }
    }
}
