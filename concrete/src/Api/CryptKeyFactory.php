<?php

namespace Concrete\Core\Api;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Environment\FunctionInspector;
use League\OAuth2\Server\CryptKey;
use phpseclib\Crypt\RSA;
use RuntimeException;

/**
 * @deprecated since ConcreteCMS v9 key handling is much easier since keys aren't stored to file, so we don't need this class
 */
class CryptKeyFactory
{
    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    private $config;

    /**
     * @var \Concrete\Core\Foundation\Environment\FunctionInspector
     */
    private $functionInspector;

    /**
     * @var \Concrete\Core\Application\Application
     */
    private $app;

    /**
     * @var string[]|null
     */
    private $keyPair;

    /**
     * @var string|null
     */
    private $keysDirectory;

    /**
     * @var \League\OAuth2\Server\CryptKey[]
     */
    private $cryptKeys = [];

    /**
     * @param \Concrete\Core\Application\Application $app used to create \phpseclib\Crypt\RSA and the DB configuration repository instances
     */
    public function __construct(Repository $config, FunctionInspector $functionInspector, Application $app)
    {
        $this->config = $config;
        $this->functionInspector = $functionInspector;
        $this->app = $app;
    }

    /**
     * @param string $handle ApiServiceProvider::KEY_PRIVATE | ApiServiceProvider::KEY_PUBLIC
     *
     * @throws \RuntimeException
     *
     * @return \League\OAuth2\Server\CryptKey
     */
    public function getCryptKey($handle)
    {
        if (!isset($this->cryptKeys[$handle])) {
            $this->cryptKeys[$handle] = $this->buildCryptKey($handle);
        }

        return $this->cryptKeys[$handle];
    }

    /**
     * @param string $handle ApiServiceProvider::KEY_PRIVATE | ApiServiceProvider::KEY_PUBLIC
     *
     * @throws \RuntimeException
     *
     * @return \League\OAuth2\Server\CryptKey
     */
    private function buildCryptKey($handle)
    {
        $file = $this->getKeyFile($handle);

        return new CryptKey(
            // $keyPath
            $file,
            // $passPhrase
            null,
            // $keyPermissionsCheck
            false // we already manage key file ownership
        );
    }

    /**
     * @param string $handle ApiServiceProvider::KEY_PRIVATE | ApiServiceProvider::KEY_PUBLIC
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    private function getKeyFile($handle)
    {
        $key = $this->getKey($handle);
        $file = $this->getKeysDirectory() . '/ccm-' . $handle . '-' . sha1($key) . '.key';
        if (!is_file($file)) {
            $this->buildKeyFile($key, $file);
        }
        
        return $file;
    }

    /**
     * @param string $handle ApiServiceProvider::KEY_PRIVATE | ApiServiceProvider::KEY_PUBLIC
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    private function getKey($handle)
    {
        if ($this->keyPair === null) {
            $dbConfig = $this->app->make('config/database');

            // See if we already have a keypair
            $keyPair = $dbConfig->get('api.keypair');
            if (!$keyPair) {
                // Generate a new keypair
                $bits = (int) $this->config->get('concrete.api.key.bits');
                $rsa = $this->app->make(RSA::class);
                $keyPair = $rsa->createKey($bits > 0 ? $bits : 2048);
                foreach ($keyPair as &$item) {
                    $item = str_replace("\r\n", "\n", $item);
                }
                // Save the keypair
                $dbConfig->set('api.keypair', $keyPair);
                $dbConfig->save('api.keypair', $keyPair);
            }
            $this->keyPair = $keyPair;
        }
        if (!isset($this->keyPair[$handle])) {
            throw new RuntimeException(t('Invalid API key handle: %s', $handle));
        }

        return $this->keyPair[$handle];
    }

    /**
     * @throws \RuntimeException
     *
     * @return string
     */
    private function getKeysDirectory()
    {
        if ($this->keysDirectory === null) {
            $dir = $this->config->get('concrete.api.key.save_path');
            if (!$dir) {
                $dir = sys_get_temp_dir();
            }
            if (!is_dir($dir)) {
                @mkdir($dir, $this->config->get('concrete.filesystem.permissions.directory'), true);
            }
            $dir = is_dir($dir) ? realpath($dir) : false;
            if (!$dir) {
                throw new RuntimeException(t('Failed to create the directory where the API key files should be stored.'));
            }
            $this->keysDirectory = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $dir), '/');
        }

        return $this->keysDirectory;
    }

    /**
     * @param string $key
     * @param string $file
     *
     * @throws \RuntimeException
     */
    private function buildKeyFile($key, $file)
    {
        if (file_put_contents($file, $key) === false) {
            throw new RuntimeException(t('Unable to save an API key file.'));
        }
        if ($this->config->get('concrete.api.key.ownership.set')) {
            try {
                if (DIRECTORY_SEPARATOR === '\\') {
                    $this->takeOwnershipWindows($file);
                } else {
                    $this->takeOwnershipPosix($file);
                }
            } catch (RuntimeException $x) {
                if ($this->config->get('concrete.api.key.ownership.force')) {
                    unlink($file);
                    throw $x;
                }
            }
        }
    }

    /**
     * @param string $file
     *
     * @throws \RuntimeException
     */
    private function takeOwnershipPosix($file)
    {
        if (chmod($file, 0600) === false) {
            throw new RuntimeException(t('Unable to set the permissions of an API key file: %s'), t('the function %s failed', 'chmod()'));
        }
    }

    /**
     * @param string $file
     *
     * @throws \RuntimeException
     */
    private function takeOwnershipWindows($file)
    {
        if (!$this->functionInspector->functionAvailable('exec')) {
            throw new RuntimeException(t('Unable to set the permissions of an API key file: %s'), t('the function %s is not available', 'exec()'));
        }
        $currentUser = get_current_user();
        if (empty($currentUser)) {
            if (empty($_ENV['USERNAME'])) {
                throw new RuntimeException(t('Unable to set the permissions of an API key file: %s'), t('unable to determine the current user'));
            }
            $currentUser = $_ENV['USERNAME'];
        }
        $output = [];
        $rc = -1;
        exec('icacls.exe ' . escapeshellarg(str_replace('/', DIRECTORY_SEPARATOR, $file)) . ' /q /inheritancelevel:r /grant ' . escapeshellarg($currentUser) . ':F 2>&1', $output, $rc);
        if ($rc !== 0) {
            throw new RuntimeException(t('Unable to set the permissions of an API key file: %s', t('the command %s failed', 'icacls') . "\n" . trim(implode("\n", $output))));
        }
    }
}
