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
        $proxyHost = $config->get('concrete.proxy.host');
        if ($proxyHost) {
            $result['proxyhost'] = $proxyHost;
            $proxyPort = $config->get('concrete.proxy.port');
            if ($proxyPort && is_numeric($proxyPort)) {
                $result['proxyport'] = $proxyPort;
            }
            $proxyUser = $config->get('concrete.proxy.user');
            if (is_string($proxyUser) && $proxyUser !== '') {
                $result['proxyuser'] = $proxyUser;
                $result['proxypass'] = (string) $config->get('concrete.proxy.password');
            }
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
        if (!isset($options['streamtmpdir']) || !$options['streamtmpdir']) {
            $options['streamtmpdir'] = Application::getFacadeApplication()->make('helper/file')->getTemporaryDirectory();
        }
        $client = new Client(null, $options);

        return $client;
    }
}
