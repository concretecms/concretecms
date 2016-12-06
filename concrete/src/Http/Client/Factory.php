<?php
namespace Concrete\Core\Http\Client;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Support\Facade\Application;

class Factory
{
    /**
     * Read the HTTP Client configuration.
     *
     * @param Repository $config
     *
     * @return array {
     *     @var bool $sslverifypeer [always]
     *     @var string $proxyhost [optional]
     *     @var int $proxyport [optional]
     *     @var string $proxyuser [optional]
     *     @var string $proxypass [optional]
     *     ... and all other options set in app.curl
     * }
     */
    protected function getOptions(Repository $config)
    {
        $result = $config->get('app.curl');
        if (!is_array($result)) {
            $result = [];
        }
        if (isset($result['verifyPeer'])) {
            $result['sslverifypeer'] = (bool) $result['verifyPeer'];
        }
        unset($result['verifyPeer']);
        $result['proxyhost'] = $config->get('concrete.proxy.host');
        $result['proxyport'] = $config->get('concrete.proxy.port');
        $result['proxyuser'] = $config->get('concrete.proxy.user');
        $result['proxypass'] = $config->get('concrete.proxy.password');

        return $result;
    }

    /**
     * Normalize the Zend HTTP Client options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function normalizeOptions(array $options)
    {
        // Default values
        $result = [
            'sslverifypeer' => true,
            'proxyhost' => '',
            'proxyport' => 8080,
            'proxyuser' => '',
            'proxypass' => '',
            'sslcafile' => null,
            'sslcapath' => null,
            'connecttimeout' => 5,
            'executetimeout' => 60,
            'keepalive' => false,
            'maxredirects' => 5,
            'rfc3986strict' => false,
            'sslcert' => null,
            'sslpassphrase' => null,
            'storeresponse' => true,
            'streamtmpdir' => null,
            'strictredirects' => false,
            'useragent' => 'concrete5 CMS',
            'encodecookies' => true,
            'httpversion' => '1.1',
            'ssltransport' => 'tls',
            'sslallowselfsigned' => false,
            'persistent' => false,
        ];
        // Normalize the given options
        foreach ($options as $key => $value) {
            // Normalize the option key (exactly like Zend Http Client)
            $key = str_replace(['-', '_', ' ', '.'], '', strtolower($key));
            switch ($key) {
                // Not nullable boolean values
                case 'sslverifypeer':
                case 'keepalive':
                case 'rfc3986strict':
                case 'storeresponse':
                case 'strictredirects':
                case 'encodecookies':
                case 'sslallowselfsigned':
                case 'persistent':
                    if ($value !== null && $value !== '') {
                        $result[$key] = (bool) $value;
                    }
                    break;
                // Not nullable integer values
                case 'proxyport':
                case 'connecttimeout':
                case 'executetimeout':
                case 'maxredirects':
                    if (is_int($value) || is_float($value) || (is_string($value) && is_numeric($value))) {
                        $result[$key] = (int) $value;
                    }
                    break;
                // Strings thay may be empty
                case 'proxyhost':
                case 'proxyuser':
                case 'proxypass':
                case 'sslpassphrase':
                case 'useragent':
                    $result[$key] = is_string($value) ? $value : '';
                    break;
                // Strings thay can't be empty
                case 'sslcafile':
                case 'sslcapath':
                case 'sslcert':
                case 'httpversion':
                case 'ssltransport':
                case 'streamtmpdir':
                    if (is_string($value) && $value !== '') {
                        $result[$key] = $value;
                    }
                    break;
                // Pass through
                default:
                    $result[$key] = $value;
                    break;
            }
        }
        // Set the temporary directory
        if ($result['streamtmpdir'] === null) {
            $result['streamtmpdir'] = Application::getFacadeApplication()->make('helper/file')->getTemporaryDirectory();
        }
        // Fix proxy options (Zend uses some of these values even if they are null)
        if ($result['proxyhost'] === '') {
            unset($result['proxyhost']);
            unset($result['proxyport']);
            unset($result['proxyuser']);
            unset($result['proxypass']);
        } elseif ($result['proxyuser'] === '') {
            unset($result['proxyuser']);
            unset($result['proxypass']);
        }
        // Don't set sslpassphrase is sslcert is not set (Zend tries to set the sslpassphrase even if no sslcert has been specified)
        if ($result['sslcert'] === null) {
            $result['sslpassphrase'] = null;
        }

        return $result;
    }

    /**
     * Create a new HTTP Client instance starting from configuration.
     *
     * @param Repository $config
     * @param string|object|null $adapter
     *
     * @return Client
     */
    public function createFromConfig(Repository $config, $adapter = null)
    {
        $options = $this->getOptions($config);

        return $this->createFromOptions($options, $adapter);
    }

    /**
     * Create a new HTTP Client instance starting from configuration.
     *
     * @param array $options See the app.curl values at concrete/config/app.php, plus the proxy options (proxyhost, proxyport, proxyuser, proxypass)
     * @param mixed $adapter The adapter to use (defaults to Curl adapter if curl extension is installed, otherwise we'll use the Socket adapter)
     *
     * @return Client
     */
    public function createFromOptions(array $options, $adapter = null)
    {
        $options = $this->normalizeOptions($options);
        if (!$adapter) {
            if (isset($options['adapter']) && $options['adapter']) {
                $adapter = $options['adapter'];
            } elseif (function_exists('curl_init')) {
                $adapter = Adapter\Curl::class;
            } else {
                $adapter = Adapter\Socket::class;
            }
        }
        $options['adapter'] = $adapter;
        if (isset($options['sslverifypeer']) && !$options['sslverifypeer']) {
            $options['sslcafile'] = null;
            $options['sslcapath'] = null;
        }
        $client = new Client(null, $options);

        return $client;
    }
}
