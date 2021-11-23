<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Database\CharacterSetCollation\Manager;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\LongRunningMigrationInterface;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20190509000000 extends AbstractMigration implements RepeatableMigrationInterface, LongRunningMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $manager = $this->app->make(Manager::class);
        try {
            $manager->reapply(
                $this->connection,
                function ($message) {
                    $this->output($message);
                }
            );
        } catch (Exception $x) {
            $this->output(t('Failed to set character sets: %s', $x->getMessage()));
        } catch (Throwable $x) {
            $this->output(t('Failed to set character sets: %s', $x->getMessage()));
        }
    }
}
