<?php
namespace Concrete\Core\Error\Handler;

use Concrete\Core\Config\Config;
use Concrete\Core\Package\PackageList;
use Core;
use Whoops\Handler\PrettyPageHandler;

/**
 * Class ErrorHandler
 *
 * @package Concrete\Core\Error\Handler
 */
class ErrorHandler extends PrettyPageHandler
{

    public function __construct()
    {
        $this->setPageTitle("concrete5 has encountered an issue.");
    }

    /**
     * {@inheritDoc}
     */
    public function handle()
    {
        if (!defined('SITE_DEBUG_LEVEL') || SITE_DEBUG_LEVEL == DEBUG_DISPLAY_ERRORS) {
            $this->addDetails();
            return parent::handle();
        }
        Core::make('helper/concrete/ui')->renderError(
            t('An unexpected error occurred.'),
            t('An error occurred while processing this request.')
        );

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
