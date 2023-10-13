<?php

namespace Concrete\Core\Install;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Encryption\PasswordHasher;

class InstallerOptionsFactory implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    public function createFromEnvironment(InstallEnvironment $environment): InstallerOptions
    {
        $options = $this->app->make(InstallerOptions::class);
        $config = $this->app->make('config');
        $configuration = [];
        $configuration['database'] = [
            'default-connection' => 'concrete',
            'connections' => [
                'concrete' => [
                    'driver' => 'concrete_pdo_mysql',
                    'server' => $environment->getDbServer(),
                    'database' => $environment->getDbDatabase(),
                    'username' => $environment->getDbUsername(),
                    'password' => $environment->getDbPassword(),
                    'character_set' => $config->get('database.fallback_character_set'),
                    'collation' => $config->get('database.fallback_collation'),
                ],
            ],
        ];
        $configuration['canonical-url'] = $environment->getCanonicalUrl() ? : null;
        $configuration['canonical-url-alternative'] = $environment->getAlternativeCanonicalUrl() ?: null;
        $configuration['session-handler'] = $environment->getSessionHandler();
        $options->setConfiguration($configuration);

        $hasher = $this->app->make(PasswordHasher::class);
        $options
            ->setPrivacyPolicyAccepted($environment->isAcceptPrivacyPolicy() ? true : false)
            ->setUserEmail($environment->getEmail())
            ->setUserPasswordHash($hasher->hashPassword($environment->getPassword()))
            ->setSiteName($environment->getSiteName())
            ->setStartingPointHandle($environment->getStartingPoint())
            ->setSiteLocaleId($environment->getSiteLocaleLanguage() . '_' . $environment->getSiteLocaleCountry())
            ->setUiLocaleId($environment->getLocale())
            ->setServerTimeZoneId($environment->getTimezone());
        return $options;
    }

}
