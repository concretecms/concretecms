<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20180716000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * @var string
     */
    const RX_MIDDLE_DFAULT = 'A-Za-z0-9_\.';

    /**
     * @var string
     */
    const RX_MIDDLE_WITHSPACES = 'A-Za-z0-9_\. ';

    /**
     * @var string
     */
    const REQUIREMENT_STRING_DEFAULT = 'A username may only contain letters, numbers, dots (not at the beginning/end), underscores (not at the beginning/end).';

    /**
     * @var string
     */
    const REQUIREMENT_STRING_WITHSPACES = 'A username may only contain letters, numbers, spaces (not at the beginning/end), dots (not at the beginning/end), underscores (not at the beginning/end).';

    /**
     * @var string
     */
    const ERROR_STRING_DEFAULT = self::REQUIREMENT_STRING_DEFAULT;

    /**
     * @var string
     */
    const ERROR_STRING_WITHSPACES = self::REQUIREMENT_STRING_WITHSPACES;

    public function upgradeDatabase()
    {
        $config = $this->app->make('config');
        if ($config->get('concrete.user.username.allow_spaces') && !$config->get('concrete.user.username.allow_spaces_migrated')) {
            $rxMiddle = $config->get('concrete.user.username.allowed_characters.middle');
            if ($rxMiddle === static::RX_MIDDLE_DFAULT) {
                $this->setAndSaveConfig($config, 'concrete.user.username.allowed_characters.middle', static::RX_MIDDLE_WITHSPACES);
            }
            if ($config->get('concrete.user.username.allowed_characters.requirement_string') === static::REQUIREMENT_STRING_DEFAULT) {
                $this->setAndSaveConfig($config, 'concrete.user.username.allowed_characters.requirement_string', static::REQUIREMENT_STRING_WITHSPACES);
            }
            if ($config->get('concrete.user.username.allowed_characters.error_string') === static::ERROR_STRING_DEFAULT) {
                $this->setAndSaveConfig($config, 'concrete.user.username.allowed_characters.error_string', static::ERROR_STRING_WITHSPACES);
            }
            $this->setAndSaveConfig($config, 'concrete.user.username.allow_spaces_migrated', true);
        }
    }

    protected function setAndSaveConfig(Repository $config, $key, $value)
    {
        $config->set($key, $value);
        $config->save($key, $value);
    }
}
