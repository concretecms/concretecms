<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Theme\AvailableVariablesUpdater;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\LongRunningMigrationInterface;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20201116182100 extends AbstractMigration implements RepeatableMigrationInterface, LongRunningMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $updater = $this->app->make(AvailableVariablesUpdater::class);
        foreach (Theme::getInstalledHandles() as $themeHandle) {
            $this->output(t('Updating style customizer variables for theme %s... ', $themeHandle));
            $theme = Theme::getByHandle($themeHandle);
            $fixResult = $updater->fixTheme($theme, AvailableVariablesUpdater::FLAG_ADD | AvailableVariablesUpdater::FLAG_UPDATE);
            $this->output((string) $fixResult);
        }
    }
}
