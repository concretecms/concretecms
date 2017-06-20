<?php
namespace Concrete\Core\Error\Handler;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Support\Facade\Database;
use Config;
use Core;
use Whoops\Handler\PrettyPageHandler;

/**
 * Class ErrorHandler.
 *
 * \@package Concrete\Core\Error\Handler
 */
class ErrorHandler extends PrettyPageHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $this->setPageTitle('concrete5 has encountered an issue.');

        $result = self::QUIT;

        $e = $this->getInspector()->getException();
        $detail = 'message';
        if ($e instanceof UserMessageException) {
            $enabled = true;
            $canBeLogged = $e->canBeLogged();
        } else {
            $enabled = (bool) Config::get('concrete.debug.display_errors');
            if ($enabled === true) {
                $detail = Config::get('concrete.debug.detail', 'message');
            }
            $canBeLogged = true;
        }
        if ($enabled) {
            if ($detail === 'debug') {
                $this->addDetails();
                $result = parent::handle();
            } else {
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

        if ($canBeLogged && Config::get('concrete.log.errors')) {
            try {
                $e = $this->getInspector()->getException();
                $db = Database::get();
                if ($db->isConnected()) {
                    $l = \Core::make('log/exceptions');
                    $l->emergency(
                        sprintf(
                            "Exception Occurred: %s:%d %s (%d)\n",
                            $e->getFile(),
                            $e->getLine(),
                            $e->getMessage(),
                            $e->getCode()
                        ),
                        [$e]
                    );
                }
            } catch (\Exception $e) {
            }
        }

        return $result;
    }

    /**
     * Add the c5 specific debug stuff.
     */
    protected function addDetails()
    {
        /*
         * General
         */
        $this->addDataTable(
            'Concrete5',
            [
                'Version' => APP_VERSION,
                'Installed Version' => Config::get('concrete.version_installed'),
            ]
        );

        /*
         * Config
         */
        $this->addDataTable('Concrete Configuration', $this->flatConfig(Config::get('concrete'), 'concrete'));
    }

    protected function flatConfig(array $config, $group)
    {
        $flat = [];
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $flat = array_merge($flat, $this->flatConfig($value, "{$group}.{$key}"));
            } elseif (is_string($value)) {
                $flat["{$group}.{$key}"] = $value;
            } else {
                $flat["{$group}.{$key}"] = json_encode($value);
            }
        }

        return $flat;
    }
}
