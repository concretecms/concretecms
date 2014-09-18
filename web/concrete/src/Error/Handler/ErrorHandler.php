<?php
namespace Concrete\Core\Error\Handler;

use Concrete\Core\Logging\Logger;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Support\Facade\Database;
use Config;
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
        $e = $this->getInspector()->getException();
        $this->setPageTitle("concrete5 has encountered an issue.");
        if (Config::get('concrete.log.errors')) {
            try {
                $db = Database::get();
                if ($db->isConnected()) {
                    $l = new Logger(LOG_TYPE_EXCEPTIONS);
                    $l->emergency(
                      t('Exception Occurred: ') . sprintf(
                          "%s:%d %s (%d)\n",
                          $e->getFile(),
                          $e->getLine(),
                          $e->getMessage(),
                          $e->getCode()
                      ), array($e)
                    );
                }
            } catch (Exception $e) {}
        }

        $display = Config::get('concrete.debug.display_errors', false);

        $detail = Config::get('concrete.debug.detail', 'message');
        $error = t('An error occurred while processing this request.');
        if ($display) {
            if ($detail === 'debug') {
                $this->addDetails();
                return parent::handle();
            }
            $error = $e->getMessage();
        }

        Core::make('helper/concrete/ui')->renderError(
            t('An unexpected error occurred.'),
            $error
        );
        Core::shutdown();

        return self::QUIT;
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
                 'Installed Version' => Config::get('concrete.version_installed')
             )
        );

        /**
         * Config
         */
        $this->addDataTable('Concrete Configuration', $this->flatConfig(Config::get('concrete'), 'concrete'));

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

    protected function flatConfig(array $config, $group) {
        $flat = array();
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $flat = array_merge($flat, $this->flatConfig($value, "{$group}.{$key}"));
            } else {
                $flat["{$group}.{$key}"] = $value;
            }
        }

        return $flat;
    }

}
