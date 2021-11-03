<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Updater\Migrations\AbstractMigration;

final class Version20211102171600 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $config = $this->app->make('config');
        $sites = $config->get('site.sites');
        foreach ($sites as $siteHandle => $configValue) {
            $configKey = sprintf('site.sites.%s.editor.ckeditor4.plugins.selected', $siteHandle);
            $editorConfig = $config->get($configKey);
            $this->output(t('Checking rich text editor settings for site: %s', $siteHandle));
            $changed = false;
            if ($editorConfig && is_array($editorConfig)) {
                $this->output(t('Editor config found.'));
                if (($key = array_search('concrete5link', $editorConfig)) !== false) {
                    $this->output(t('Found `concrete5link` plugin in editor config. Updating to `concretelink`'));
                    unset($editorConfig[$key]);
                    $editorConfig[] = 'concretelink';
                    $changed = true;
                }

                if (($key = array_search('concrete5filemanager', $editorConfig)) !== false) {
                    $this->output(t('Found `concrete5filemanager` plugin in editor config. Updating to `concretefilemanager`'));
                    unset($editorConfig[$key]);
                    $editorConfig[] = 'concretefilemanager';
                    $changed = true;
                }
                if (($key = array_search('concrete5inline', $editorConfig)) !== false) {
                    $this->output(t('Found `concrete5inline` plugin in editor config. Updating to `concreteinline`'));
                    unset($editorConfig[$key]);
                    $editorConfig[] = 'concreteinline';
                    $changed = true;
                }
                if (($key = array_search('concrete5styles', $editorConfig)) !== false) {
                    $this->output(t('Found `concrete5styles` plugin in editor config. Updating to `concretestyles`'));
                    unset($editorConfig[$key]);
                    $editorConfig[] = 'concretestyles';
                    $changed = true;
                }
                if (($key = array_search('concrete5uploadimage', $editorConfig)) !== false) {
                    $this->output(t('Found `concrete5uploadimage` plugin in editor config. Updating to `concreteuploadimage`'));
                    unset($editorConfig[$key]);
                    $editorConfig[] = 'concreteuploadimage';
                    $changed = true;
                }

                if ($changed) {
                    $this->output(t('Updating config values for new plugins...'));
                    $config->save($configKey, $editorConfig);
                } else {
                    $this->output(t('No changes to config necessary...'));
                }
            }
        }
    }

}
