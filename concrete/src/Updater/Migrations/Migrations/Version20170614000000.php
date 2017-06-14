<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170614000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $service = \Core::make('site');
        $site = $service->getDefault();
        $siteConfig = $site->getConfigRepository();
        $url = $siteConfig->get('seo.canonical_ssl_url');
        if ($url) {
            $siteConfig->save('seo.canonical_url_alternative', $url);
        }
    }

    public function down(Schema $schema)
    {
    }
}
