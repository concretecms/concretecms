<?php
namespace Concrete\Core\Feed;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Laminas\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Laminas\Feed\Reader\Http\Psr7ResponseDecorator;
use GuzzleHttp\Client;

class GuzzleClient implements FeedReaderHttpClientInterface
{

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @param GuzzleClientInterface|null $client
     */
    public function __construct(GuzzleClientInterface $client = null)
    {
        $this->client = $client ?: new Client();
    }

    /**
     * {@inheritdoc}
     */
    public function get($uri)
    {
        return new Psr7ResponseDecorator(
            $this->client->request('GET', $uri)
        );
    }

}
