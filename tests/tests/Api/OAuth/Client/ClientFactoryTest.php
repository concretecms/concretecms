<?php

namespace Concrete\Tests\Api\OAuth\Client;

use Concrete\Core\Api\OAuth\Client\ClientFactory;
use Concrete\Core\Application\Application;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\UuidGenerator;
use Mockery as M;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit_Framework_TestCase;

class ClientFactoryTest extends PHPUnit_Framework_TestCase
{
    use MockeryPHPUnitIntegration;
    protected $app;
    protected $em;
    protected $uuidGenerator;

    /** @var ClientFactory|\Mockery\Mock */
    protected $factory;

    /**
     * @before
     */
    public function before()
    {
        $this->app = M::mock(Application::class);
        $this->em = M::mock(EntityManager::class);
        $this->uuidGenerator = M::mock(UuidGenerator::class);

        $this->factory = new ClientFactory($this->app, $this->em, $this->uuidGenerator);

        // Handle "build"
        $this->app->shouldReceive('build')->andReturnUsing(function($abstract, $data=[]) {
            return M::mock($abstract, $data)->makePartial();
        });

        // Handle "make"
        $this->app->shouldReceive('make')->andReturnUsing([$this->app, 'build']);

        // Handle UUIDs
        $this->uuidGenerator->shouldReceive('generate')->andReturn('uu-i-d');
    }

    /**
     * @after
     */
    public function after()
    {
        $this->app = $this->em = $this->uuidGenerator = $this->factory = null;
    }

    public function testCredentials()
    {
        $defaultKeyLength = 64;
        $defaultSecretLength = 96;
        $customKeyLength = 17; // Low prime number
        $customSecretLength = 97; // High prime number
        $credentials1 = $this->factory->generateCredentials();
        $credentials2 = $this->factory->generateCredentials();
        $credentials3 = $this->factory->generateCredentials($customKeyLength, $customSecretLength);

        // Make sure none of the credentials are empty
        $this->assertNotEmpty($credentials1->getKey(), 'Credentials generated with empty key!');
        $this->assertNotEmpty($credentials2->getKey(), 'Credentials generated with empty key!');
        $this->assertNotEmpty($credentials1->getSecret(), 'Credentials generated with empty secret!');
        $this->assertNotEmpty($credentials2->getSecret(), 'Credentials generated with empty secret!');

        // Make sure the keys aren't the same as the secrets
        $this->assertNotSame($credentials1->getKey(), $credentials1->getSecret(), 'Credentials generated with equivalent key and secret!');
        $this->assertNotSame($credentials2->getKey(), $credentials2->getSecret(), 'Credentials generated with equivalent key and secret!');

        // Make sure the two credentials aren't the same at all
        $this->assertNotSame($credentials1->getKey(), $credentials2->getKey(), 'Credentials generated with the same key twice!');
        $this->assertNotSame($credentials1->getSecret(), $credentials2->getSecret(), 'Credentials generated with the same secret twice!');

        // Make sure the size is right
        $this->assertEquals($defaultKeyLength, strlen($credentials1->getKey()), 'Default size key is the wrong size.');
        $this->assertEquals($defaultSecretLength, strlen($credentials1->getSecret()), 'Custom size secret is the wrong size.');
        $this->assertEquals($customKeyLength, strlen($credentials3->getKey()), 'Custom size key is the wrong size.');
        $this->assertEquals($customSecretLength, strlen($credentials3->getSecret()), 'Custom size secret is the wrong size.');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage A key must have a length longer than 16
     */
    public function testShortKeysException()
    {
        $this->factory->generateCredentials(10, 10);
    }

    /**
     * @todo Enable scopes when scopes is implemented
     */
    public function testCreateClient()
    {
        $client = $this->factory->createClient(
            'Ritas Toaster',
            'http://example.com',
            ['test', 'scopes'],
            'key',
            'secret'
        );

        $this->assertSame(
            [
                'uu-i-d',
                'Ritas Toaster',
                'http://example.com',
                //['test', 'scopes'],
                'key',
                'secret'
            ],
            [
                $client->getIdentifier(),
                $client->getName(),
                $client->getRedirectUri(),
                //$client->getScopes(),
                $client->getClientKey(),
                $client->getClientSecret()
            ]
        );
    }

}
