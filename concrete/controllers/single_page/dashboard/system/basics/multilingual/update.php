<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Basics\Multilingual;

use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Localization\Service\TranslationsChecker;
use Concrete\Core\Localization\Service\TranslationsInstaller;
use Concrete\Core\Localization\Translation\Local\Stats as LocalStats;
use Concrete\Core\Localization\Translation\LocaleStatus;
use Concrete\Core\Localization\Translation\PackageLocaleStatus;
use Concrete\Core\Localization\Translation\Remote\Stats as RemoteStats;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Exception;

class Update extends DashboardPageController
{
    public function view()
    {
        $this->set('data', $this->getData());
    }

    private function getData()
    {
        $translationsChecker = $this->app->make(TranslationsChecker::class);
        /* @var TranslationsChecker $translationsChecker */
        $data = array_merge([$translationsChecker->getCoreTranslations()], $translationsChecker->getPackagesTranslations());
        $data = array_filter($data, function (LocaleStatus $status) {
            return !empty($status->getInstalledUpdated()) || !empty($status->getInstalledOutdated()) || !empty($status->getOnlyRemote());
        });

        return $data;
    }

    public function install_package_locale($packageHandle, $localeID)
    {
        $rf = $this->app->make(ResponseFactoryInterface::class);
        /* @var ResponseFactoryInterface $rf */
        try {
            if (!$this->token->validate("install-package-locale-{$packageHandle}@{$localeID}")) {
                throw new Exception($this->token->getErrorMessage());
            }
            $installer = $this->app->make(TranslationsInstaller::class);
            /* @var TranslationsInstaller $installer */
            if ($packageHandle === 'concrete5') {
                $installer->installCoreTranslations($localeID);
            } else {
                $package = null;
                $ps = $this->app->make(PackageService::class);
                /* @var PackageService $ps */
                foreach ($ps->getAvailablePackages(false) as $pkg) {
                    if ($pkg->getPackageHandle() === $packageHandle) {
                        $package = $pkg;
                        break;
                    }
                }
                if ($package === null) {
                    throw new Exception(t('Unable to find the specified package'));
                }
                $installer->installPackageTranslations($package, $localeID);
            }
            Localization::clearCache();
        } catch (Exception $x) {
            return $rf->json(['error' => true, 'errors' => [$x->getMessage()]]);
        }

        return $rf->json(true);
    }

    public function update_all_outdated()
    {
        try {
            if ($this->token->validate('update-all-outdated')) {
                $numUpdated = 0;
                $installer = $this->app->make(TranslationsInstaller::class);
                /* @var TranslationsInstaller $installer */
                foreach ($this->getData() as $details) {
                    $package = ($details instanceof PackageLocaleStatus) ? $details->getPackage() : null;
                    foreach ($details->getInstalledOutdated() as $localeID => $rl) {
                        if ($package === null) {
                            $installer->installCoreTranslations($localeID);
                        } else {
                            $installer->installPackageTranslations($package, $localeID);
                        }
                        ++$numUpdated;
                    }
                }
                $msg = t2('%s language has been updated', '%s languages have been updated', $numUpdated);
                if ($numUpdated > 0) {
                    Localization::clearCache();
                }
                $this->flash('success', $msg);
            } else {
                $this->flash('error', $this->token->getErrorMessage());
            }
        } catch (Exception $x) {
            $this->flash('error', nl2br(h($x->getMessage())));
        }
        $this->redirect($this->action(''));
    }

    public function getLocaleRowHtml($localeID, $handle, RemoteStats $remote = null, LocalStats $local = null, $action = '')
    {
        $dateHelper = $this->app->make('date');
        $hLocaleID = h($localeID);
        $hLocaleName = h(\Punic\Language::getName($localeID));
        switch ($action) {
            case 'update':
                $button = '<button class="btn btn-xs btn-primary ccm-install-package-locale" data-is-update="true" data-token="' . h($this->token->generate("install-package-locale-{$handle}@" . $localeID)) . '" data-action="' . h($this->action('install_package_locale', $handle, $localeID)) . '">' . t('Update') . '</button>';
                break;
            case 'install':
                $button = '<button class="btn btn-xs btn-info ccm-install-package-locale" data-token="' . h($this->token->generate("install-package-locale-{$handle}@" . $localeID)) . '" data-action="' . h($this->action('install_package_locale', $handle, $localeID)) . '">' . t('Install') . '</button>';
                break;
                break;
            default:
                $button = '';
                break;
        }

        $dialogTitle = t('Language Details');
        $dialogUrl = \URL::to('/ccm/system/dialogs/language/update/details') . '?cID=' . $this->request->getCurrentPage()->getCollectionID() . '&locale=' . rawurlencode($localeID);
        if ($handle !== 'concrete5') {
            $dialogUrl .= '&pkgHandle=' . rawurldecode($handle);
        }

        $result = '<tr>';
        if ($remote === null) {
            $result .= '<td></td>';
            $hUpdatedOn = h(tc('DateTime', 'Updated: %s', $dateHelper->formatPrettyDateTime($local->getUpdatedOn(), true)));
        } else {
            $progress = $remote->getProgress();
            $progressTitle = h(t2('%1$s translated string out of %2$s', '%1$s translated strings out of %2$s', $remote->getTranslated(), $remote->getTotal()));
            $hUpdatedOn = h(tc('DateTime', 'Updated: %s', $dateHelper->formatPrettyDateTime($remote->getUpdatedOn(), true)));
            $result .= <<<EOT
    <td>
        <div class="progress launch-tooltip" title="{$progressTitle}">
            <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{$progress}" aria-valuemin="0" aria-valuemax="100" style="width: {$progress}%;">
                <small>{$progress}%</small>
            </div>
        </div>
    </td>
EOT
            ;
        }
        $result .= <<<EOT
    <td><code>{$hLocaleID}</code></td>
    <td><a class="dialog-launch" dialog-width="580" dialog-height="490" dialog-modal="true" dialog-title="{$dialogTitle}" href="{$dialogUrl}">{$hLocaleName}</a></td>
    <td class="hidden-xs">{$hUpdatedOn}</td>
    <td>{$button}</td>
</tr>
EOT
        ;

        return $result;
    }
}
