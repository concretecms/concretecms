<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * Removes showinfo (Youtube ingore it now)
 * Updates the show related videos description (Youtube always show related videos)
 * https://developers.google.com/youtube/player_parameters?hl=en#rel
 * @see https://github.com/concrete5/concrete5/issues/8158
 */
class Version20191003000327 extends AbstractMigration  implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->refreshBlockType('youtube');
    }
}
