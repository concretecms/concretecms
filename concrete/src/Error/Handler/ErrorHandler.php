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
                $this->registerHideList();
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
                'Version' => Config::get('concrete.version'),
                'Installed Version' => Config::get('concrete.version_installed'),
                'Database Version' => Config::get('concrete.version_db'),
            ]
        );

        /*
         * Config
         */
        $this->addDataTable('Concrete Configuration', $this->flatConfig($this->cleanedConfig(Config::get('concrete'), 'concrete'), 'concrete'));
    }

    protected function cleanedConfig(array $config, $group)
    {
        $clean = [];
        foreach ($config as $key => $value) {
            $assembled = "{$group}.{$key}";
            if (is_array($value)) {
                $clean[$key] = $this->cleanedConfig($value, $assembled);
            } elseif ($this->isKeyHidden($assembled)) {
                $clean[$key] = str_repeat('*', is_string($value) ? strlen($value) : 3);
            } else {
                $clean[$key] = $value;
            }
        }

        return $clean;
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

    protected function registerHideList()
    {
        foreach (\Concrete\Core\Support\Facade\Config::get('concrete.debug.hide_keys', []) as $superGlobal => $keys) {
            if ($superGlobal === 'config') {
                foreach ($keys as $key) {
                    $this->hideConfigKeys[$key] = true;
                }
                continue;
            }

            foreach ((array) $keys as $key) {
                $this->hideSuperglobalKey($superGlobal, $key);
            }
        }
    }

    private function isKeyHidden($key)
    {
        // We have to check each node to make sure it's not hidden:
        $key = explode('.', $key);
        $length = count($key);

        do {
            $tryKey = implode('.', array_slice($key, 0, $length));
            if (isset($this->hideConfigKeys[$tryKey])) {
                return true;
            }
        } while ($length-- > 1);

        return false;
    }
}
