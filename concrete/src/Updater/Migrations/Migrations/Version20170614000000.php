<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20170614000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $service = \Core::make('site');
        $site = $service->getDefault();
        $siteConfig = $site->getConfigRepository();
        $url = $siteConfig->get('seo.canonical_ssl_url');
        if ($url) {
            if ($siteConfig->get('seo.canonical_url')) {
                $siteConfig->save('seo.canonical_url_alternative', $url);
            } else {
                $siteConfig->save('seo.canonical_url', $url);
            }
            $siteConfig->save('seo.canonical_ssl_url', null);
        }
    }
}
