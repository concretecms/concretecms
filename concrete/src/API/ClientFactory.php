<?php
namespace Concrete\Core\API;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;

class ClientFactory
{

    public function createClient()
    {
        $client = new Client();
        $description = new Description([
            'baseUri' => 'http://httpbin.org/',
            'operations' => [
                'testing' => [
                    'httpMethod' => 'GET',
                    'uri' => '/get{?foo}',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'foo' => [
                            'type' => 'string',
                            'location' => 'uri'
                        ],
                        'bar' => [
                            'type' => 'string',
                            'location' => 'query'
                        ]
                    ]
                ]
            ],
            'models' => [
                'getResponse' => [
                    'type' => 'object',
                    'additionalProperties' => [
                        'location' => 'json'
                    ]
                ]
            ]
        ]);

        $guzzleClient = new GuzzleClient($client, $description);

        $result = $guzzleClient->testing(['foo' => 'bar']);
        echo $result['args']['foo'];

    }


}