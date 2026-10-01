<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\OAuth\AccessToken;
use Concrete\Core\Entity\OAuth\AuthCode;
use Concrete\Core\Entity\OAuth\RefreshToken;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

defined('C5_EXECUTE') or die('Access Denied.');

final class Version20260930000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        // The expiry dates of the OAuth entities are now mapped as datetime_immutable
        $this->refreshEntities([
            AccessToken::class,
            AuthCode::class,
            RefreshToken::class,
        ]);
    }
}
