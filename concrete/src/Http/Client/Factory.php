<?php
namespace Concrete\Core\Http\Client;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use League\Url\Url;

class Factory
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

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
        $result = $config->get('app.http_client');
        if (!is_array($result)) {
            $result = [];
        }

        $host = $config->get('concrete.proxy.host');
        $port = $config->get('concrete.proxy.port');
        $user = $config->get('concrete.proxy.user');
        $pass = $config->get('concrete.proxy.password');

        if ($host) {
            $url = Url::createFromUrl($host);
            $url->setUser($user);
            $url->setPass($pass);
            $url->setPort($port);
        }

        if (isset($url)) {
            $result['proxy'] = [
                'http' => (string) $url
            ];
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
     * @param array $options See the app.http_client values at concrete/config/app.php, plus the concrete.proxy values at concrete/config/concrete.php.
     * @param mixed $handler Optionally specify a specific handler.
     *
     * @return Client
     */
    public function createFromOptions(array $options, $handler = null)
    {
        if ($handler) {
            if (is_string($handler)) {
                $handler = new $handler();
            }
            $options['handler'] = $handler;
        }

        $client = new Client($options);

        if (isset($options['logger'])) {
            if (is_object($options['logger'])) {
                $client->setLogger($options['logger']);
            } else {
                $client->setLogger($this->app->make($options['logger']));
            }
        }

        return $client;
    }
}
