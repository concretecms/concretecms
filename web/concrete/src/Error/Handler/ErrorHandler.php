<?php
namespace Concrete\Core\Error\Handler;

use Concrete\Core\Logging\Logger;
use Concrete\Core\Support\Facade\Database;
use Config;
use Core;
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

        $result = self::QUIT;

        $enabled = Config::get('concrete.debug.display_errors');

        if ($enabled) {
            $detail = Config::get('concrete.debug.detail', 'message');
            if ($detail === 'debug') {
                $this->addDetails();
                $result = parent::handle();
            } else {
                $e = $this->getInspector()->getException();
                Core::make('helper/concrete/ui')->renderError(
                    t('An unexpected error occurred.'),
                    h($e->getMessage())
                );
            }
        } else {
            Core::make('helper/concrete/ui')->renderError(
                t('An unexpected error occurred.'),
                t('An error occurred while processing this request.')
            );
        }

        if (Config::get('concrete.log.errors')) {
            try {
                $e = $this->getInspector()->getException();
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
                        ),
                        array($e)
                    );
                }
            } catch (\Exception $e) {
            }
        }

        return $result;
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
    }

    protected function flatConfig(array $config, $group)
    {
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
