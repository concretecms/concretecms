<?php
namespace Concrete\Core\Error\Handler;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Support\Facade\Database;
use Concrete\Core\Support\Facade\Config;
use Core;
use Whoops\Handler\PrettyPageHandler;

/**
 * Class ErrorHandler.
 *
 * \@package Concrete\Core\Error\Handler
 */
class ErrorHandler extends PrettyPageHandler
{

    private $hideConfigKeys = [];

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $this->setPageTitle('Concrete CMS has encountered an issue.');

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
            'Concrete CMS',
            [
                'Version' => Config::get('concrete.version'),
                'Installed Version' => Config::get('concrete.version_installed'),
                'Database Version' => Config::get('concrete.version_db'),
            ]
        );

        $extensions = collect(array_flip(get_loaded_extensions()))->map(function($value, string $ext) {
            return phpversion($ext) ?: 'Unknown version';
        });
        $this->addDataTable(
            'PHP',
            [
                'Version' => PHP_VERSION,
                'Extensions' => $extensions->all(),
            ]
        );

        $this->addDataTable('Concrete Configuration', [
            'concrete' => $this->cleanedConfig(Config::get('concrete'), 'concrete'),
            'app' => $this->cleanedConfig(Config::get('app'), 'app')
        ]);
    }

    protected function cleanedConfig(array $config, $group): array
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

    /**
     * @deprecated Will be removed
     */
    protected function flatConfig(array $config, $group)
    {
        $flat = [];
        foreach ($config as $key => $value) {
            $assembled = "{$group}.{$key}";
            if ($this->isKeyHidden($assembled)) {
                if (is_array($value)) {
                    $flat[$assembled] = '[***]';
                } else {
                    $flat[$assembled] = str_repeat('*', is_string($value) ? strlen($value) : 3);
                }

                continue;
            }

            if (is_array($value)) {
                $flat = array_merge($flat, $this->flatConfig($value, "{$group}.{$key}"));
            } elseif (is_string($value)) {
                $flat[$assembled] = $value;
            } else {
                $flat[$assembled] = json_encode($value);
            }
        }

        return $flat;
    }

    protected function registerHideList(): void
    {
        foreach (Config::get('concrete.debug.hide_keys', []) as $superGlobal => $keys) {
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

    private function isKeyHidden(string $key): bool
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
