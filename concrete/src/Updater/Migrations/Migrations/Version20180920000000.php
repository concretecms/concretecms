<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20180920000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->createSinglePage('/dashboard/system/permissions/trusted_proxies', 'Trusted Proxies', ['meta_keywords' => 'trusted, proxy, proxies, ip, header, cloudflare']);
    }
}
