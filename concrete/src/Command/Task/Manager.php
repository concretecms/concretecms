<?php
namespace Concrete\Core\Command\Task;

use Concrete\Core\Application\Application;
use Concrete\Core\Command\Task\Controller\ControllerInterface;
use Concrete\Core\Support\Manager as CoreManager;

defined('C5_EXECUTE') or die("Access Denied.");

class Manager extends CoreManager
{
    /**
     * @deprecated use driver('rescan_files') instead
     */
    public function createRescanFilesDriver()
    {
        return $this->createDriverFromHandle('rescan_files');
    }

    /**
     * @deprecated use driver('clear_cache') instead
     */
    public function createClearCacheDriver()
    {
        return $this->createDriverFromHandle('clear_cache');
    }

    /**
     * @deprecated use driver('generate_sitemap') instead
     */
    public function createGenerateSitemapDriver()
    {
        return $this->createDriverFromHandle('generate_sitemap');
    }

    /**
     * @deprecated use driver('check_automated_groups') instead
     */
    public function createCheckAutomatedGroupsDriver()
    {
        return $this->createDriverFromHandle('check_automated_groups');
    }

    /**
     * @deprecated use driver('deactivate_users') instead
     */
    public function createDeactivateUsersDriver()
    {
        return $this->createDriverFromHandle('deactivate_users');
    }

    /**
     * @deprecated use driver('generate_thumbnails') instead
     */
    public function createGenerateThumbnailsDriver()
    {
        return $this->createDriverFromHandle('generate_thumbnails');
    }

    /**
     * @deprecated use driver('update_statistics') instead
     */
    public function createUpdateStatisticsDriver()
    {
        return $this->createDriverFromHandle('update_statistics');
    }

    /**
     * @deprecated use driver('remove_old_page_versions') instead
     */
    public function createRemoveOldPageVersionsDriver()
    {
        return $this->createDriverFromHandle('remove_old_page_versions');
    }

    /**
     * @deprecated use driver('reindex_content') instead
     */
    public function createReindexContentDriver()
    {
        return $this->createDriverFromHandle('reindex_content');
    }

    /**
     * @deprecated use driver('process_email') instead
     */
    public function createProcessEmailDriver()
    {
        return $this->createDriverFromHandle('process_email');
    }

    /**
     * @deprecated use driver('remove_old_file_attachments') instead
     */
    public function createRemoveOldFileAttachmentsDriver()
    {
        return $this->createDriverFromHandle('remove_old_file_attachments');
    }

    /**
     * @deprecated use driver('remove_unvalidated_users') instead
     */
    public function createRemoveUnvalidatedUsersDriver()
    {
        return $this->createDriverFromHandle('remove_unvalidated_users');
    }

    /**
     * @deprecated use driver('production_status') instead
     */
    public function createProductionStatusDriver()
    {
        return $this->createDriverFromHandle('production_status');
    }

    /**
     * @deprecated use driver('custom_javascript_report') instead
     */
    public function createCustomJavascriptReportDriver()
    {
        return $this->createDriverFromHandle('custom_javascript_report');
    }

    /**
     * @deprecated use driver('page_cache_report') instead
     */
    public function createPageCacheReportDriver()
    {
        return $this->createDriverFromHandle('page_cache_report');
    }

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    /**
     * {@inheritdoc}
     *
     * Drivers that are neither registered with extend() nor created by a create...Driver() method are built from the task handle.
     *
     * @param string $driver the task handle (for tasks defined by packages: the package handle and the task handle, separated by a colon)
     *
     * @see \Concrete\Core\Support\Manager::createDriver()
     * @see \Concrete\Core\Command\Task\Manager::createDriverFromHandle()
     */
    protected function createDriver($driver)
    {
        $driver = (string) $driver;
        if (strpos($driver, ':') === false) {
            $pkgHandle = '';
            $handle = $driver;
        } else {
            [$pkgHandle, $handle] = explode(':', $driver, 2);
        }
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        }
        if (isset($this->customCreators[$handle])) {
            return $this->callCustomCreator($handle);
        }
        $method = 'create' . camelcase($handle) . 'Driver';
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        return $this->createDriverFromHandle($handle, $pkgHandle);
    }

    /**
     * Create a task controller given the handle of the task:
     * - "my_task" is resolved as \Concrete\Core\Command\Task\Controller\MyTaskController
     * - "my_task" of the "my_package" package is resolved as \Concrete\Package\MyPackage\Command\Task\Controller\MyTaskController.
     *
     * @throws \InvalidArgumentException if the controller class doesn't exist
     */
    protected function createDriverFromHandle(string $handle, string $pkgHandle = ''): ControllerInterface
    {
        if ($handle !== '') {
            $class = core_class('Core\\Command\\Task\\Controller\\' . camelcase($handle) . 'Controller', $pkgHandle === '' ? false : $pkgHandle);
            if (class_exists($class) && is_a($class, ControllerInterface::class, true)) {
                return $this->container->make($class);
            }
        }

        throw new \InvalidArgumentException('Driver [' . ($pkgHandle === '' ? $handle : "{$pkgHandle}:{$handle}") . '] not supported.');
    }
}
