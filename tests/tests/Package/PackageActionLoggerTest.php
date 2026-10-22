<?php

declare(strict_types=1);

namespace Concrete\Tests\Package;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageActionLogger;
use Concrete\Tests\TestCase;
use Mockery as M;
use Psr\Log\LoggerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class PackageActionLoggerTest extends TestCase
{
    public function testLogsInstalledPackage(): void
    {
        $logger = $this->createLogger('Package "Migration Tool" (migration_tool) 9.1.0 installed.');

        $logger->logInstalled($this->createPackage('9.1.0'));
    }

    public function testLogsUpdatedPackage(): void
    {
        $logger = $this->createLogger('Package "Migration Tool" (migration_tool) updated from 9.1.0 to 9.2.0.');

        $logger->logUpdated($this->createPackage('9.2.0'), '9.1.0');
    }

    public function testLogsUninstalledPackageUsingInstalledVersion(): void
    {
        $logger = $this->createLogger('Package "Migration Tool" (migration_tool) 9.1.0 uninstalled.');
        $package = $this->createPackage('9.2.0');
        $package->setPackageEntity($this->createEntity('9.1.0'));

        $logger->logUninstalled($package);
    }

    public function testLogsInstallFailure(): void
    {
        $logger = $this->createLogger('Package "Migration Tool" (migration_tool) 9.2.0 install failed: This package requires Concrete CMS 9.3 or greater.');

        $logger->logInstallFailed(
            $this->createPackage('9.2.0'),
            'This package requires Concrete CMS 9.3 or greater.'
        );
    }

    public function testLogsUpdateFailure(): void
    {
        $logger = $this->createLogger('Package "Migration Tool" (migration_tool) 9.2.0 update failed: Upgrade blocked.');

        $logger->logUpdateFailed($this->createPackage('9.2.0'), 'Upgrade blocked.');
    }

    public function testLogsUninstallFailureUsingInstalledVersion(): void
    {
        $logger = $this->createLogger('Package "Migration Tool" (migration_tool) 9.1.0 uninstall failed: Theme is active.');
        $package = $this->createPackage('9.2.0');
        $package->setPackageEntity($this->createEntity('9.1.0'));

        $logger->logUninstallFailed($package, 'Theme is active.');
    }

    public function testLogsEveryErrorInTheList(): void
    {
        $errors = new ErrorList();
        $errors->add('First reason.');
        $errors->add('Second reason.');
        $logger = $this->createLogger("Package \"Migration Tool\" (migration_tool) 9.2.0 install failed: First reason.\nSecond reason.");

        $logger->logInstallFailed($this->createPackage('9.2.0'), $errors);
    }

    public function testStripsHtmlFromUserMessageException(): void
    {
        $reason = (new UserMessageException('<strong>Not allowed</strong>'))->setMessageContainsHtml(true);
        $logger = $this->createLogger('Package "Migration Tool" (migration_tool) 9.2.0 install failed: Not allowed');

        $logger->logInstallFailed($this->createPackage('9.2.0'), $reason);
    }

    public function testEmptyPackageNameFallsBackToHandle(): void
    {
        $logger = $this->createLogger('Package "migration_tool" (migration_tool) 9.1.0 installed.');

        $logger->logInstalled($this->createPackage('9.1.0', ''));
    }

    private function createLogger(string $expectedMessage): PackageActionLogger
    {
        $psrLogger = M::mock(LoggerInterface::class);
        $psrLogger->shouldReceive('notice')->once()->with($expectedMessage);

        $factory = M::mock(LoggerFactory::class);
        $factory->shouldReceive('createLogger')->once()->with(Channels::CHANNEL_PACKAGES)->andReturn($psrLogger);

        return new PackageActionLogger($factory);
    }

    private function createPackage(string $version, string $name = 'Migration Tool'): Package
    {
        $app = M::mock(Application::class);

        return new class($app, $name, $version) extends Package {
            /**
             * @var string
             */
            private $handle = 'migration_tool';

            /**
             * @var string
             */
            private $name;

            /**
             * @var string
             */
            private $version;

            public function __construct(Application $app, string $name, string $version)
            {
                parent::__construct($app);
                $this->name = $name;
                $this->version = $version;
            }

            public function getPackageHandle()
            {
                return $this->handle;
            }

            public function getPackageName()
            {
                return $this->name === '' ? '' : t($this->name);
            }

            public function getPackageVersion()
            {
                return $this->version;
            }
        };
    }

    private function createEntity(string $version): PackageEntity
    {
        $entity = M::mock(PackageEntity::class);
        $entity->shouldReceive('getPackageVersion')->andReturn($version);

        return $entity;
    }
}
