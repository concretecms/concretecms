<?php
namespace Concrete\Core\Error\Handler;

use Concrete\Core\Config\Config;
use Concrete\Core\Logging\Log;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Support\Facade\Database;
use Core;
use Whoops\Example\Exception;
use Whoops\Handler\PrettyPageHandler;

/**
 * Class ErrorHandler
 *
 * @package Concrete\Core\Error\Handler
 */
class ErrorHandler extends PrettyPageHandler
{

    /**
     * {@inheritDoc}
     */
    public function handle()
    {
        $this->setPageTitle("concrete5 has encountered an issue.");
        if (defined('ENABLE_LOG_ERRORS') && ENABLE_LOG_ERRORS) {
            try {
                $e = $this->getInspector()->getException();
                $db = Database::get();
                if ($db->isConnected()) {
                    $l = new Log(LOG_TYPE_EXCEPTIONS, true, true);
                    $l->write(
                      t('Exception Occurred: ') . sprintf(
                          "%s:%d %s (%d)\n",
                          $e->getFile(),
                          $e->getLine(),
                          $e->getMessage(),
                          $e->getCode()
                      )
                    );
                    $l->write($e->getTraceAsString());
                    $l->close();
                }
            } catch (Exception $e) {}
        }

        $debug = intval(defined('SITE_DEBUG_LEVEL') ? SITE_DEBUG_LEVEL : Config::get('SITE_DEBUG_LEVEL'), 10);
        if ($debug === DEBUG_DISPLAY_ERRORS) {
            $this->addDetails();
            return parent::handle();
        }

        Core::make('helper/concrete/ui')->renderError(
            t('An unexpected error occurred.'),
            t('An error occurred while processing this request.')
        );
        Core::shutdown();

    }

    /**
     * Add the c5 specific debug stuff
     */
    protected function addDetails()
    {
        /**
         * General
         */
        $this->addDataTable(
             'Concrete5',
             array(
                 'Version'           => APP_VERSION,
                 'Installed Version' => Config::get('SITE_INSTALLED_APP_VERSION')
             )
        );

        /**
         * Cache
         */
        $this->addDataTable(
             'Cache',
             array(
                 'Block Cache'        => ENABLE_BLOCK_CACHE ? 'ON' : 'OFF',
                 'Overrides Cache'    => ENABLE_OVERRIDE_CACHE ? 'ON' : 'OFF',
                 'Full Page'          => FULL_PAGE_CACHE_GLOBAL ? 'ON' : 'OFF',
                 'Full Page Lifetime' => defined('FULL_PAGE_CACHE_LIFETIME') ? FULL_PAGE_CACHE_LIFETIME : 'Default',
             )
        );

        /**
         * Installed Packages
         */
        $pla = PackageList::get();
        $pl = $pla->getPackages();
        $packages = array();
        foreach ($pl as $p) {
            if ($p->isPackageInstalled()) {
                $packages[$p->getPackageName()] = $p->getPackageVersion();
            }
        }

        $this->addDataTable('Installed Packages', $packages);
    }

}
