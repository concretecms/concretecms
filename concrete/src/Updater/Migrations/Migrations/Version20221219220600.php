<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\User\UserInfoRepository;

final class Version20221219220600 extends AbstractMigration implements RepeatableMigrationInterface
{
    private const DEFAULT_SENDER_KEY = 'concrete.email.default.address';

    // List of configuration keys to be updated to avoid BC breaks
    private const OTHER_SENDER_KEYS = [
        'concrete.email.forgot_password.address',
        'concrete.email.form_block.address',
        'concrete.email.register_notification.address',
        'concrete.email.workflow_notification.address',
    ];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        // Before we used the admin's email as the default address:
        // let's avoid BC breaks by restoring that behaviour.
        $adminAddress = $this->getAdminAddress();
        if ($adminAddress === '') {
            $this->write(t('Default system email address not updated because we failed to retrieve the admin email address.'));
            return;
        }
        $config = $this->app->make(Repository::class);
        $defaultAddress = (string) $config->get(self::DEFAULT_SENDER_KEY);
        if ($defaultAddress === $adminAddress) {
            $this->write(t("We don't have to update the system email addresses because the default address doesn't change."));
            return;
        }
        foreach (self::OTHER_SENDER_KEYS as $key) {
            if ((string) $config->get($key) !== '') {
                // This configuration key is configured: we don't need to restore the previous address
                continue;
            }
            $this->write(t('Updating email sender key: %s', $key));
            $config->set($key, $adminAddress);
            $config->save($key, $adminAddress);
        }
    }

    private function getAdminAddress(): string
    {
        $adminUser = $this->app->make(UserInfoRepository::class)->getByID(USER_SUPER_ID);

        return $adminUser ? (string) $adminUser->getUserEmail() : '';
    }
}
