<?php
declare(strict_types=1);

namespace Concrete\Tests\Api\OAuth\Client;

use Concrete\Core\Api\OAuth\Client\ClientFactory;
use Concrete\Core\Application\Application;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\UuidGenerator;
use Mockery as M;
use Concrete\Tests\TestCase;

class ClientFactoryTest extends TestCase
{
    protected $app;
    protected $em;
    protected $uuidGenerator;

    /** @var ClientFactory|\Mockery\Mock */
    protected $factory;

    /**
     * @before
     */
    public function before():void
    {
        $this->app = M::mock(Application::class);
        $this->em = M::mock(EntityManager::class);
        $this->uuidGenerator = M::mock(UuidGenerator::class);

        $this->factory = new ClientFactory($this->app, $this->em, $this->uuidGenerator);

        // Handle "build"
        $this->app->shouldReceive('build')->andReturnUsing(function($abstract, $data=[]) {
            switch ($abstract) {
                case 'Concrete\Core\Api\OAuth\Client\Credentials':
                    return app($abstract, $data);
                default:
                    return M::mock($abstract, $data)->makePartial();
            }
        });

        // Handle "make"
        $this->app->shouldReceive('make')->andReturnUsing([$this->app, 'build']);

        // Handle UUIDs
        $this->uuidGenerator->shouldReceive('generate')->andReturn('uu-i-d');
    }

    /**
     * @after
     */
    public function after(): void
    {
        $this->app = $this->em = $this->uuidGenerator = $this->factory = null;
    }

    /**
     * @covers \Concrete\Core\Api\OAuth\Client\ClientFactory::generateCredentials
     */
    public function testCredentials(): void
    {
        $defaultKeyLength = 64;
        $defaultSecretLength = 96;
        $customKeyLength = 17; // Low prime number
        $customSecretLength = 97; // High prime number
        $credentials1 = $this->factory->generateCredentials();
        $credentials2 = $this->factory->generateCredentials();
        $credentials3 = $this->factory->generateCredentials($customKeyLength, $customSecretLength);

        // Make sure none of the credentials are empty
        self::assertNotEmpty($credentials1->getKey(), 'Credentials generated with empty key!');
        self::assertNotEmpty($credentials2->getKey(), 'Credentials generated with empty key!');
        self::assertNotEmpty($credentials1->getSecret(), 'Credentials generated with empty secret!');
        self::assertNotEmpty($credentials2->getSecret(), 'Credentials generated with empty secret!');

        // Make sure the keys aren't the same as the secrets
        self::assertNotSame($credentials1->getKey(), $credentials1->getSecret(), 'Credentials generated with equivalent key and secret!');
        self::assertNotSame($credentials2->getKey(), $credentials2->getSecret(), 'Credentials generated with equivalent key and secret!');

        // Make sure the two credentials aren't the same at all
        self::assertNotSame($credentials1->getKey(), $credentials2->getKey(), 'Credentials generated with the same key twice!');
        self::assertNotSame($credentials1->getSecret(), $credentials2->getSecret(), 'Credentials generated with the same secret twice!');

        // Make sure the size is right
        self::assertEquals($defaultKeyLength, strlen($credentials1->getKey()), 'Default size key is the wrong size.');
        self::assertEquals($defaultSecretLength, strlen($credentials1->getSecret()), 'Custom size secret is the wrong size.');
        self::assertEquals($customKeyLength, strlen($credentials3->getKey()), 'Custom size key is the wrong size.');
        self::assertEquals($customSecretLength, strlen($credentials3->getSecret()), 'Custom size secret is the wrong size.');
    }

    /**
     * @covers \Concrete\Core\Api\OAuth\Client\ClientFactory::generateCredentials
     * @return void
     */
    public function testShortKeysException():void
    {
       $this->expectException(\InvalidArgumentException::class);
       $this->expectExceptionMessage('A key must have a length longer than 16');
       $this->factory->generateCredentials(10, 10);
    }

    /**
     * @covers \Concrete\Core\Api\OAuth\Client\ClientFactory::createClient
     */
    public function testCreateClient():void
    {
        $client = $this->factory->createClient(
            'Ritas Toaster',
            'http://example.com',
            ['test', 'scopes'],
            'key',
            'secret'
        );

        self::assertSame(
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
        return;
    }

}
