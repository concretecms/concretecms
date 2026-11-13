<?php

declare(strict_types=1);

namespace Concrete\Core\Package;

use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Writes package install, update, uninstall, and directory-move outcomes to the Packages log channel.
 */
class PackageActionLogger
{
    /**
     * @var LoggerFactory
     */
    protected $loggerFactory;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->loggerFactory = $loggerFactory;
    }

    public function logInstalled(Package $package): void
    {
        $this->notice(t(
            'Package "%1$s" (%2$s) %3$s installed.',
            $this->getPackageName($package),
            $package->getPackageHandle(),
            $this->getAttemptVersion($package)
        ));
    }

    /**
     * @param string $fromVersion installed version before upgradeCoreData()
     */
    public function logUpdated(Package $package, $fromVersion): void
    {
        $this->notice(t(
            'Package "%1$s" (%2$s) updated from %3$s to %4$s.',
            $this->getPackageName($package),
            $package->getPackageHandle(),
            (string) $fromVersion,
            $this->getAttemptVersion($package)
        ));
    }

    public function logUninstalled(Package $package): void
    {
        $this->notice(t(
            'Package "%1$s" (%2$s) %3$s uninstalled.',
            $this->getPackageName($package),
            $package->getPackageHandle(),
            $this->getInstalledVersion($package)
        ));
    }

    /**
     * @param ErrorList|UserMessageException|string $reason
     */
    public function logInstallFailed(Package $package, $reason): void
    {
        $this->notice(t(
            'Package "%1$s" (%2$s) %3$s install failed: %4$s',
            $this->getPackageName($package),
            $package->getPackageHandle(),
            $this->getAttemptVersion($package),
            $this->describeReason($reason)
        ));
    }

    /**
     * @param ErrorList|UserMessageException|string $reason
     */
    public function logUpdateFailed(Package $package, $reason): void
    {
        $this->notice(t(
            'Package "%1$s" (%2$s) %3$s update failed: %4$s',
            $this->getPackageName($package),
            $package->getPackageHandle(),
            $this->getAttemptVersion($package),
            $this->describeReason($reason)
        ));
    }

    /**
     * @param ErrorList|UserMessageException|string $reason
     */
    public function logUninstallFailed(Package $package, $reason): void
    {
        $this->notice(t(
            'Package "%1$s" (%2$s) %3$s uninstall failed: %4$s',
            $this->getPackageName($package),
            $package->getPackageHandle(),
            $this->getInstalledVersion($package),
            $this->describeReason($reason)
        ));
    }

    public function logDirectoryMoved(Package $package, string $destination): void
    {
        $this->notice(t(
            'Package "%1$s" (%2$s) %3$s directory moved to %4$s.',
            $this->getPackageName($package),
            $package->getPackageHandle(),
            $this->getInstalledVersion($package),
            $destination
        ));
    }

    /**
     * @param ErrorList|UserMessageException|string $reason
     */
    public function logDirectoryMoveFailed(Package $package, $reason, ?string $destination = null): void
    {
        $name = $this->getPackageName($package);
        $handle = $package->getPackageHandle();
        $version = $this->getInstalledVersion($package);
        $described = $this->describeReason($reason);
        if ($destination !== null && $destination !== '') {
            $this->notice(t(
                'Package "%1$s" (%2$s) %3$s directory move failed: %4$s. Intended destination: %5$s.',
                $name,
                $handle,
                $version,
                $described,
                $destination
            ));

            return;
        }

        $this->notice(t(
            'Package "%1$s" (%2$s) %3$s directory move failed: %4$s',
            $name,
            $handle,
            $version,
            $described
        ));
    }

    /**
     * Version the install or update attempt is acting on.
     * Install uses the package being installed. Update uses the version being upgraded to.
     */
    private function getAttemptVersion(Package $package): string
    {
        return $package->getPackageVersion();
    }

    /**
     * Version currently installed. Falls back to the controller version when the entity is already gone.
     */
    private function getInstalledVersion(Package $package): string
    {
        $entity = $package->getPackageEntity();
        if ($entity instanceof PackageEntity) {
            $version = (string) $entity->getPackageVersion();
            if ($version !== '') {
                return $version;
            }
        }

        return $package->getPackageVersion();
    }

    private function getPackageName(Package $package): string
    {
        $name = t($package->getPackageName());
        if ($name === '') {
            return $package->getPackageHandle();
        }

        return $name;
    }

    /**
     * @param ErrorList|UserMessageException|string $reason
     */
    private function describeReason($reason): string
    {
        if ($reason instanceof ErrorList) {
            return $reason->toText();
        }
        if ($reason instanceof UserMessageException) {
            $message = $reason->getMessage();
            if ($reason->messageContainsHtml()) {
                return strip_tags($message);
            }

            return $message;
        }

        return (string) $reason;
    }

    private function notice(string $message): void
    {
        $this->loggerFactory->createLogger(Channels::CHANNEL_PACKAGES)->notice($message);
    }
}
