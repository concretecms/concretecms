<?php

namespace Concrete\Tests\Marketplace;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\File\Service\File;
use Concrete\Core\Marketplace\Connection;
use Concrete\Core\Marketplace\ConnectionInterface;
use Concrete\Core\Marketplace\Exception\InvalidConnectResponseException;
use Concrete\Core\Marketplace\Exception\InvalidPackageException;
use Concrete\Core\Marketplace\Exception\PackageAlreadyExistsException;
use Concrete\Core\Marketplace\Exception\UnableToConnectException;
use Concrete\Core\Marketplace\Model\RemotePackage;
use Concrete\Core\Marketplace\PackageRepository;
use Concrete\Core\Site\Service;
use Concrete\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PackageRepositoryTest extends TestCase
{
    /**
     * @var Client&MockInterface
     */
    private $client;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Repository&MockInterface
     */
    private $config;

    /** @var RemotePackage */
    private $fakePackage;

    /** @var Service&MockInterface */
    private $siteService;

    /** @var File&MockInterface */
    private $fileService;

    /** @var Site&MockInterface */
    private $fakeSite;

    /**
     * @before
     */
    public function beforeEach(): void
    {
        $this->client = \Mockery::spy(Client::class);
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->config = \Mockery::spy(Repository::class);
        $this->siteService = \Mockery::spy(Service::class);
        $this->fileService = \Mockery::mock(File::class);
        $this->fakeSite = Mockery::spy(Site::class);
        $this->siteService->shouldReceive('getDefault')->andReturn($this->fakeSite);
        $this->fileService->shouldReceive('getTemporaryDirectory')->andReturn('/tmp');
        $this->fakePackage = new RemotePackage(
            'foo',
            '',
            '',
            '',
            'http://download/path',
            '',
            '',
            '',
            '',
            []
        );
    }

    protected function repository(string $baseUri = '', array $paths = []): PackageRepository
    {
        return new PackageRepository(
            $this->client,
            $this->serializer,
            $this->config,
            $this->config,
            $this->siteService,
            $this->fileService,
            $baseUri,
            $paths
        );
    }

    public function testRequestCreation(): void
    {
        $baseUri = 'test://foo.baz';
        $paths = [
            'fake_key' => '/fake/%s/%s/path'
        ];

        $this->fakeSite->expects('getSiteCanonicalURL')->andReturn('fake_url');
        $this->fakeSite->expects('getSiteName')->andReturn('fake_name');
        $this->config->expects('get')->with('concrete.version')->andReturns('fake_version');

        $repository = $this->repository($baseUri, $paths);
        $method = new \ReflectionMethod($repository, 'requestFor');
        $method->setAccessible(true);

        $request = $method->invoke($repository, 'FOO', 'fake_key', 'foo', 'baz');
        assert($request instanceof Request);

        $this->assertEquals('FOO', $request->getMethod());
        $this->assertEquals('foo.baz', $request->getUri()->getHost());
        $this->assertEquals('test', $request->getUri()->getScheme());
        $this->assertEquals('/fake/foo/baz/path', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame([
            'csiURL' => 'fake_url',
            'csiName' => 'fake_name',
            'csiVersion' => 'fake_version',
            'ms' => '0',
        ], $query);
    }

    public function testRequestAuthentication(): void
    {
        $repository = $this->repository();
        $method = new \ReflectionMethod($repository, 'authenticate');
        $method->setAccessible(true);

        $connection = new Connection('public', 'private');

        /** @var Request $sha256 */
        $sha256 = $method->invoke($repository, new Request('GET', new Uri()), $connection);
        /** @var Request $sha512 */
        $sha512 = $method->invoke($repository, new Request('GET', new Uri()), $connection, 'sha512');

        $now = new \DateTimeImmutable();
        $time = $now->setTime((int) $now->format('h'), (int) $now->format('i'));
        $sha256Nonce = 'sha256,' . hash_hmac('sha256', (string) $time->getTimestamp(), $connection->getPrivate());
        $sha512Nonce = 'sha512,' . hash_hmac('sha512', (string) $time->getTimestamp(), $connection->getPrivate());

        $this->assertEquals('public:' . $sha256Nonce, $sha256->getUri()->getUserInfo());
        $this->assertEquals('public:' . $sha512Nonce, $sha512->getUri()->getUserInfo());
    }

    public function testValidate(): void
    {
        $repository = $this->repository('http://foo', ['connect_validate' => '/connect/validate']);
        $this->client->expects('send')->zeroOrMoreTimes(3)->withArgs(function(Request $request) {
            $uri = $request->getUri();
            if (!$uri->getUserInfo() || $uri->getPath() !== '/connect/validate') {
                return false;
            }

            return true;
        })->andReturn(
            new Response(400, [], ''),
            new Response(200, [], "'foo'"),
            new Response(200, [], '{}'),
            new Response(200, [], '{"valid":true}'),
            new Response(200, [], '{"valid":false,"site":"fff"}'),
            new Response(200, [], '{"valid":true,"site":"fff"}'),
        );

        $connection = new Connection('pub', 'priv');
        $this->assertFalse($repository->validate($connection));
        $this->assertFalse($repository->validate($connection));
        $this->assertFalse($repository->validate($connection));
        $this->assertFalse($repository->validate($connection));
        $this->assertFalse($repository->validate($connection));
        $this->assertTrue($repository->validate($connection));
    }


    protected function connectRequestReturns(string $path, bool $withUserInfo, $returns = [], $throw = null): void
    {
        $expectation = $this->client->expects('send')->withArgs(function(Request $request) use ($path, $withUserInfo) {
            $uri = $request->getUri();
            if ((bool) $uri->getUserInfo() !== $withUserInfo || $uri->getPath() !== $path) {
                return false;
            }

            return true;
        });

        if ($returns) {
            $expectation->times(count($returns))->andReturn(...$returns);
        }

        if ($throw) {
            $expectation->once()->andThrow($throw);
        }
    }

    public function testConnectBadResponse(): void
    {
        $repository = $this->repository('http://foo', ['connect' => '/connect']);

        $fakeRequest = new Request('GET', new Uri());
        $fakeResponse = new Response(409);
        $this->connectRequestReturns('/connect', false, null, new ClientException('', $fakeRequest, $fakeResponse));

        $this->expectException(UnableToConnectException::class);
        $this->expectExceptionCode(409);
        $repository->connect();
    }

    public function testConnectErrorResponse(): void
    {
        $repository = $this->repository('http://foo', ['connect' => '/connect']);

        $this->connectRequestReturns('/connect', false, [
            new Response(200, [], '{"error": "foo", "code": 433}')
        ]);

        $this->expectException(UnableToConnectException::class);
        $this->expectExceptionCode(433);
        $this->expectExceptionMessage('foo');
        $repository->connect();
    }

    public function testConnectInvalidResponse(): void
    {
        $repository = $this->repository('http://foo', ['connect' => '/connect']);

        $this->connectRequestReturns('/connect', false, [
            new Response(200, [], '{"error" => "foo", "code": 433}')
        ]);

        $this->expectException(InvalidConnectResponseException::class);
        $repository->connect();
    }

    public function testConnectHappyPath(): void
    {
        $repository = $this->repository('http://foo', ['connect' => '/connect']);

        $this->connectRequestReturns('/connect', false, [
            new Response(200, [], '{"public":"fff","private":"ggg","site_id":"hh"}')
        ]);

        $result = $repository->connect();
        $this->assertEquals('fff', $result->getPublic());
        $this->assertEquals('ggg', $result->getPrivate());
    }

    public function testGetConnection(): void
    {
        $key = 'concrete.marketplace.key';
        $this->config->expects('get')->times(3)->with("{$key}.public")
            ->andReturn(
                '',
                'public',
                'public',
            );
        $this->config->expects('get')->times(3)->with("{$key}.private")
            ->andReturn(
                'private',
                '',
                'private',
            );

        $repository = $this->repository();
        $this->assertNull($repository->getConnection());
        $this->assertNull($repository->getConnection());
        $connection = $repository->getConnection();
        $this->assertInstanceOf(ConnectionInterface::class, $connection);
        $this->assertEquals('public', $connection->getPublic());
        $this->assertEquals('private', $connection->getPrivate());
    }

    public function testGetPackage(): void
    {
        $connection = new Connection('foo', 'bar');
        $repository = $this->repository('http://foo.com', [
            'get' => '/get/package'
        ]);

        $this->connectRequestReturns('/get/package', true, [], \Mockery::mock(BadResponseException::class));
        $this->assertNull($repository->getPackage($connection, 'pkg_id'));

        $this->connectRequestReturns('/get/package', true, [new Response(200, [], '{fff}')]);
        $this->assertNull($repository->getPackage($connection, 'pkg_id'));

        $this->connectRequestReturns('/get/package', true, [new Response(200, [], '{"id":1}')]);
        $this->assertNull($repository->getPackage($connection, 'pkg_id'));

        $this->connectRequestReturns('/get/package', true, [
            new Response(200, [], json_encode([
                'handle' => '',
                'name' => '',
                'description' => '',
                'summary' => '',
                'download' => '',
                'icon' => '',
                'id' => '',
                'version' => '',
                'file_description' => '',
                'compatibility' => [1, 2, 3, 4, 5],
            ], JSON_THROW_ON_ERROR))
        ]);
        $found = $repository->getPackage($connection, 'pkg_id');
        $this->assertInstanceOf(RemotePackage::class, $found);
    }

    public function testGetPackages(): void
    {
        $connection = new Connection('foo', 'bar');
        $repository = $this->repository('http://foo.com', [
            'list' => '/get/packages'
        ]);

        $this->connectRequestReturns('/get/packages', true, [], \Mockery::mock(BadResponseException::class));
        $this->assertEmpty($repository->getPackages($connection));

        $this->connectRequestReturns('/get/packages', true, [new Response(200, [], '{fff}')]);
        $this->assertEmpty($repository->getPackages($connection));

        $this->connectRequestReturns('/get/packages', true, [new Response(200, [], '{"id":1}')]);
        $this->assertEmpty($repository->getPackages($connection));

        $package = function(string $handle, string $id, string $version, array $compat = null) {
            $compat = $compat ?? [1, 2, 3, 4, 5];
            return [
                'handle' => $handle,
                'name' => '',
                'description' => '',
                'summary' => '',
                'download' => '',
                'icon' => '',
                'id' => $id,
                'version' => $version,
                'file_description' => '',
                'compatibility' => $compat,
            ];
        };

        $workingResponse = function() use ($package) {
            return new Response(200, [], json_encode([
                $package('foo', '1234', '1.2.3', ['1.0.0']),
                $package('foo', '1235', '1.2.4'),
                $package('baz', '1236', '1.0.0'),
            ], JSON_THROW_ON_ERROR));
        };

        $this->connectRequestReturns('/get/packages', true, [
            $workingResponse(), $workingResponse(), $workingResponse(), $workingResponse()
        ]);
        $found = $repository->getPackages($connection);
        $this->assertCount(3, $found);
        $this->assertContainsOnlyInstancesOf(RemotePackage::class, $found);

        $this->config->expects('get')->with('concrete.version')->zeroOrMoreTimes()->andReturn('1.0.0');
        $found = $repository->getPackages($connection, true);
        $this->assertCount(2, $found);
        $this->assertContainsOnlyInstancesOf(RemotePackage::class, $found);

        $found = $repository->getPackages($connection, false, true);
        $this->assertCount(1, $found);
        $this->assertContainsOnlyInstancesOf(RemotePackage::class, $found);

        $found = $repository->getPackages($connection, true, true);
        $this->assertCount(1, $found);
        $this->assertContainsOnlyInstancesOf(RemotePackage::class, $found);
        $this->assertEquals('1.2.3', $found[0]->version);
    }

    protected function expectFileDownload(): void
    {
        $this->client->expects('send')->withArgs(function(Request $request, array $options) {
            if (!isset($options[RequestOptions::SINK])) {
                return false;
            }
            copy(__DIR__ . '/../../assets/Marketplace/foo.zip', $options[RequestOptions::SINK]);
            return true;
        });
    }

    public function testDownloadHappyPath(): void
    {
        $connection = new Connection('pub', 'priv');
        $this->expectFileDownload();

        $repository = $this->repository();
        $repository->download($connection, $this->fakePackage);

        $this->assertTrue(file_exists(DIR_PACKAGES . '/foo/controller.php'));
        $this->assertEquals("worked\n", file_get_contents(DIR_PACKAGES . '/foo/controller.php'));
    }

    /**
     * @dataProvider downloadPackageAlreadyExistsProvider
     */
    public function testDownloadPackageAlreadyExists(bool $overwrite): void
    {
        mkdir(DIR_PACKAGES . '/foo');
        touch(DIR_PACKAGES . '/foo/controller.php');
        file_put_contents(DIR_PACKAGES . '/foo/controller.php', 'old');

        $connection = new Connection('pub', 'priv');
        $repository = $this->repository();
        $e = null;

        if ($overwrite) {
            $this->expectFileDownload();
        }
        try {
            $repository->download($connection, $this->fakePackage, $overwrite);
        } catch (\Throwable $e) {}

        if (!$overwrite) {
            $this->assertInstanceOf(PackageAlreadyExistsException::class, $e);
            $this->assertEquals('old', file_get_contents(DIR_PACKAGES . '/foo/controller.php'));
        } else {
            $this->assertEquals("worked\n", file_get_contents(DIR_PACKAGES . '/foo/controller.php'));
        }
    }

    public function downloadPackageAlreadyExistsProvider()
    {
        return [
            [true],
            [false],
        ];
    }


    public function testDownloadPackageInvalid(): void
    {
        mkdir(DIR_PACKAGES . '/baz');
        touch(DIR_PACKAGES . '/baz/controller.php');
        file_put_contents(DIR_PACKAGES . '/baz/controller.php', 'old');

        $this->expectFileDownload();
        $connection = new Connection('pub', 'priv');
        $repository = $this->repository();
        $e = null;
        $this->fakePackage->handle = 'baz';

        try {
            $repository->download($connection, $this->fakePackage, true);
        } catch (\Throwable $e) {}

        $this->assertInstanceOf(InvalidPackageException::class, $e);
        $this->assertEquals('old', file_get_contents(DIR_PACKAGES . '/baz/controller.php'));
    }

    /**
     * @after
     */
    public function afterEach()
    {
        foreach (['foo', 'baz'] as $dir) {
            if (is_dir(DIR_PACKAGES . '/' . $dir)) {
                unlink(DIR_PACKAGES . '/' . $dir . '/controller.php');
                rmdir(DIR_PACKAGES . '/' . $dir );
            }
        }
    }
}
