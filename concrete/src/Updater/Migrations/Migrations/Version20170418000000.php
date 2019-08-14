<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * @since 8.2.0
 */
class Version20170418000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     * @since 8.3.2
     */
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            // Technically only the notification form submission entity is new but we need all of the entities
            // included so that all the foreign keys are created.
            'Concrete\Core\Entity\Notification\Notification',
            'Concrete\Core\Entity\Express\Entry',
            'Concrete\Core\Entity\Notification\NewFormSubmissionNotification',
        ]);
    }
}
