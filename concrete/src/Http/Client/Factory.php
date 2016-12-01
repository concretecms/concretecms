<?php
namespace Concrete\Core\Http\Client;

use Concrete\Core\Config\Repository\Repository;

class Factory
{
    /**
     * @param Repository $config
     *
     * @return array {
     *
     *    @var bool $sslverifypeer [always]
     *    @var string $proxyhost [optional]
     *    @var int $proxyport [optional]
     *    @var string $proxyuser [optional]
     *    @var string $proxypass [optional]
     *    @var int $connectiontimeout [optional]
     *    @var int $responsetimeout [optional]
     *
     *    @param string $sslcafile [optional]
     *    @param string $sslcapath [optional]
     * }
     */
    protected function getOptions(Repository $config)
    {
        $result = [
            'sslverifypeer' => (bool) $config->get('app.curl.verifyPeer'),
        ];
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
        $connectionTimeout = $config->get('app.curl.connectionTimeout');
        if (is_numeric($connectionTimeout)) {
            $result['connectiontimeout'] = (int) $connectionTimeout;
        }
        $responseTimeout = $config->get('app.curl.responseTimeout');
        if (is_numeric($responseTimeout)) {
            $result['responsetimeout'] = (int) $responseTimeout;
        }
        $cainfo = $config->get('app.curl.caInfo');
        if (is_string($cainfo) && $cainfo !== '') {
            $result['sslcafile'] = $cainfo;
        }
        $capath = $config->get('app.curl.caPath');
        if (is_string($capath) && $capath !== '') {
            $result['sslcapath'] = $path;
        }

        return $result;
    }

    /**
     * @return \Concrete\Core\Http\Client\Client
     */
    public function createFromConfig(Repository $config, $adapter = null)
    {
        if ($adapter === null) {
            if (function_exists('curl_init')) {
                $adapter = Adapter\Curl::class;
            } else {
                $adapter = Adapter\Socket::class;
            }
        }
        $options = $this->getOptions($config);
        $options['adapter'] = $adapter;
        $client = new Client(null, $options);
        $adapter = $client->getAdapter();
        if ($adapter instanceof \Zend\Http\Client\Adapter\Curl) {
            if (isset($options['sslcafile'])) {
                $adapter->setCurlOption(CURLOPT_CAINFO, $options['sslcafile']);
            }
            if (isset($options['sslcapath'])) {
                $adapter->setCurlOption(CURLOPT_CAPATH, $options['capath']);
            }
        }

        return $client;
    }
}
