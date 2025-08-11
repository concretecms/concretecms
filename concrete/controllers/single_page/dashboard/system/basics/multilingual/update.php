<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics\Multilingual;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Localization\Service\TranslationsChecker;
use Concrete\Core\Localization\Service\TranslationsInstaller;
use Concrete\Core\Localization\Translation\Local\FactoryInterface as LocalFactory;
use Concrete\Core\Localization\Translation\Local\Stats as LocalStats;
use Concrete\Core\Localization\Translation\LocalRemoteCouple;
use Concrete\Core\Localization\Translation\Remote\Stats as RemoteStats;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Symfony\Component\HttpFoundation\JsonResponse;

defined('C5_EXECUTE') or die('Access Denied.');

class Update extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make(Repository::class);
        $this->set('translateUrlPrefix', rtrim((string) $config->get('concrete.i18n.community_translation.package_url', ''), '/'));
        $this->set('currentLocale', $this->app->make(Localization::class)->getLocale());
        $this->set('packages', $this->app->make(PackageService::class)->getAvailablePackages(false));
    }

    public function fetchState(): JsonResponse
    {
        if (!$this->token->validate('ccm-ml-fetch')) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $translationsChecker = $this->app->make(TranslationsChecker::class);
        if ($this->request->request->getBoolean('core')) {
            $result = $translationsChecker->getCoreTranslations();
        } else {
            $package = null;
            $packageHandle = (string) $this->request->request->get('package', '');
            if ($packageHandle !== '') {
                $ps = $this->app->make(PackageService::class);
                foreach ($ps->getAvailablePackages(false) as $p) {
                    if ($p->getPackageHandle() === $packageHandle) {
                        $package = $p;
                        break;
                    }
                }
            }
            if ($package !== null) {
                $result = $translationsChecker->getPackageTranslations($package);
            } else {
                throw new UserMessageException(t('Invalid data received.'));
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json([
            'updated' => array_map(
                function (LocalRemoteCouple $couple) { return $this->serializeLocalRemoteCouple($couple); },
                $result->getInstalledUpdated(),
            ) ?: null,
            'outdated' => array_map(
                function (LocalRemoteCouple $couple) { return $this->serializeLocalRemoteCouple($couple); },
                $result->getInstalledOutdated(),
            ) ?: null,
            'onlyLocal' => array_map(
                function (LocalStats $stats) { return ['local' => $this->serializeLocalStats($stats), 'remote' => null]; },
                $result->getOnlyLocal(),
            ) ?: null,
            'onlyRemote' => array_map(
                function (RemoteStats $stats) { return ['local' => null, 'remote' => $this->serializeRemoteStats($stats)]; },
                $result->getOnlyRemote(),
            ) ?: null,
        ]);
    }

    public function install(): JsonResponse
    {
        if (!$this->token->validate('ccm-ml-install')) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        if (!($localeID = $this->request->request->get('localeID'))) {
            throw new UserMessageException(t('Invalid data received.'));
        }
        $installer = $this->app->make(TranslationsInstaller::class);
        $localFactory = $this->app->make(LocalFactory::class);
        if ($this->request->request->getBoolean('core')) {
            $installer->installCoreTranslations($localeID);
            $result = $localFactory->getCoreStats($localeID);
        } else {
            $package = null;
            $packageHandle = (string) $this->request->request->get('package', '');
            if ($packageHandle !== '') {
                $ps = $this->app->make(PackageService::class);
                foreach ($ps->getAvailablePackages(false) as $p) {
                    if ($p->getPackageHandle() === $packageHandle) {
                        $package = $p;
                        break;
                    }
                }
            }
            if ($package !== null) {
                $installer->installPackageTranslations($package, $localeID);
                $result = $localFactory->getPackageStats($package, $localeID);
            } else {
                throw new UserMessageException(t('Invalid data received.'));
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(
            $this->serializeLocalStats($result)
        );
    }

    private function serializeLocalRemoteCouple(LocalRemoteCouple $couple): array
    {
        return [
            'local' => $this->serializeLocalStats($couple->getLocalStats()),
            'remote' => $this->serializeRemoteStats($couple->getRemoteStats()),
        ];
    }

    private function serializeLocalStats(LocalStats $stats): array
    {
        return [
            'version' => $stats->getVersion(),
            'updatedOn' => $stats->getUpdatedOn() ? $stats->getUpdatedOn()->getTimestamp() : null,
            'file' => $stats->getFileDisplayName(),
        ];
    }
    
    private function serializeRemoteStats(RemoteStats $stats): array
    {
        return [
            'version' => $stats->getVersion(),
            'updatedOn' => $stats->getUpdatedOn() ? $stats->getUpdatedOn()->getTimestamp() : null,
            'total' => $stats->getTotal(),
            'translated' => $stats->getTranslated(),
            'progress' => $stats->getProgress(),
        ];
    }
}
