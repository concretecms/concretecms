<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Site\Service;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Config\Repository\Repository;

class Version20240809105200 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $config = $this->app->make(Repository::class);
        if (!$config->get('concrete.marketplace.intelligent_search')) {
            $this->output(t('Marketplace Intelligent Search is disabled'));
        } elseif ($config->get('concrete.urls.concrete5_secure') !== 'https://marketplace.concretecms.com') {
            $this->output(t('Marketplace Intelligent Search not disabled since the %s configuration has been customized', 'concrete.urls.concrete5_secure'));
        } elseif ($config->get('concrete.urls.paths.marketplace.remote_item_list') !== '/marketplace/') {
            $this->output(t('Marketplace Intelligent Search not disabled since the %s configuration has been customized', 'concrete.urls.paths.marketplace.remote_item_list'));
        } else {
            $config->save('concrete.marketplace.intelligent_search', false);
            $this->output(t('Marketplace Intelligent Search has been disabled'));
        }
    }
}
