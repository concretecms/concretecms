<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Symfony\Component\HttpFoundation\Request;

class Version20190530123000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        // Convert trusted headers from Symfony 3.2- to Symfony 3.3+
        $config = $this->app->make('config');
        $configKey = 'concrete.security.trusted_proxies.headers';
        $headers = $config->get($configKey);
        if (is_array($headers)) {
            $flags = $this->convertNamesToFlags($headers);
        } else {
            $flags = (int) $headers;
        }
        $config->set($configKey, $flags);
        $config->save($configKey, $flags);
    }

    /**
     * Get the mapping from the Symfony 3.2- header names and the Symfony 3.3+ bit flags.
     *
     * @return array
     */
    protected function getNameToFlagMap()
    {
        return [
            'forwarded' => Request::HEADER_FORWARDED,
            'client_ip' => Request::HEADER_X_FORWARDED_FOR,
            'client_host' => Request::HEADER_X_FORWARDED_HOST,
            'client_proto' => Request::HEADER_X_FORWARDED_PROTO,
            'client_port' => Request::HEADER_X_FORWARDED_PORT,
        ];
    }

    /**
     * Convert a list of Symfony 3.2- header names to an integer using the Symfony 3.3+ bit flags.
     *
     * @param string[] $names
     *
     * @return int
     */
    protected function convertNamesToFlags(array $names)
    {
        $result = 0;
        $map = $this->getNameToFlagMap();
        foreach ($names as $name) {
            if (isset($map[$name])) {
                $result |= $map[$name];
            }
        }

        return $result;
    }
}
