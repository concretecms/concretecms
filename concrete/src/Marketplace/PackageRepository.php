<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Marketplace\Exception\InvalidConnectResponseException;
use Concrete\Core\Marketplace\Exception\InvalidPackageException;
use Concrete\Core\Marketplace\Exception\PackageAlreadyExistsException;
use Concrete\Core\Marketplace\Exception\UnableToConnectException;
use Concrete\Core\Marketplace\Exception\UnableToPlacePackageException;
use Concrete\Core\Marketplace\Model\ConnectError;
use Concrete\Core\Marketplace\Model\ConnectResult;
use Concrete\Core\Marketplace\Model\RemotePackage;
use Concrete\Core\Marketplace\Model\ValidateResult;
use Concrete\Core\Marketplace\Update\UpdatedFieldInterface;
use Concrete\Core\Site\Service;
use Concrete\Core\Url\Resolver\CanonicalUrlResolver;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use GuzzleHttp\Psr7\Utils;

final class PackageRepository implements PackageRepositoryInterface
{
    private Client $client;
    private Serializer $serializer;
    private Repository $config;
    private Application $app;
    private Service $siteService;
    private CanonicalUrlResolver $resolver;
    private string $baseUri;
    private array $paths;

    public function __construct(
        Client $client,
        Serializer $serializer,
        Repository $config,
        Application $app,
        Service $siteService,
        CanonicalUrlResolver $resolver,
        string $baseUri,
        array $paths
    ) {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->config = $config;
        $this->app = $app;
        $this->siteService = $siteService;
        $this->resolver = $resolver;
        $this->baseUri = $baseUri;
        $this->paths = $paths;
    }

    public function getPackage(ConnectionInterface $connection, string $packageId): ?RemotePackage
    {
        $request = $this->authenticate($this->requestFor('GET', 'get', $packageId), $connection);
        try {
            $response = $this->client->send($request);
            $data = json_decode($response->getBody()->getContents(), true, 4, JSON_THROW_ON_ERROR);
            $result = $this->serializer->denormalize($data, RemotePackage::class);
        } catch (BadResponseException|\JsonException|ExceptionInterface $e) {
            return null;
        }

        return $result;
    }

    public function getPackages(ConnectionInterface $connection, bool $latestOnly = false, bool $compatibleOnly = false): array
    {
        $request = $this->authenticate($this->requestFor('GET', 'list'), $connection);
        try {
            $response = $this->client->send($request);
            $data = json_decode($response->getBody()->getContents(), true, 4, JSON_THROW_ON_ERROR);
            /** @var RemotePackage[] $result */
            $result = array_map(fn($item) => $this->serializer->denormalize($item, RemotePackage::class), $data);
        } catch (BadResponseException|\JsonException|ExceptionInterface $e) {
            return [];
        }

        if ($compatibleOnly) {
            $me = $this->config->get('concrete.version');
            $result = array_filter($result, fn($item) => in_array($me, $item->compatibility, true));
        }

        $latest = [];
        if ($latestOnly) {
            foreach ($result as $item) {
                $current = $latest[$item->handle] ?? null;
                if (!$current) {
                    $latest[$item->handle] = $item;
                    continue;
                }

                if (version_compare($item->version, $current->version, '>')) {
                    $latest[$item->handle] = $item;
                }
            }
            $result = array_values($latest);
        }

        return $result;
    }

    public function download(ConnectionInterface $connection, RemotePackage $package, bool $overwrite = false): void
    {
        if (!$overwrite && file_exists(DIR_PACKAGES . '/' . $package->handle)) {
            throw new PackageAlreadyExistsException();
        }

        // Try to start the download
        $request = $this->authenticate(new Request('GET', new Uri($package->download)), $connection);
        $output = tempnam('/tmp', $package->handle);
        $this->client->send($request, [
            RequestOptions::ALLOW_REDIRECTS => true,
            RequestOptions::SINK => $output,
        ]);

        // Unzip the archive
        $unzipPath = '/tmp/' . uniqid($package->handle, true);
        $archive = new \ZipArchive();
        $archive->open($output);
        $archive->extractTo($unzipPath);
        $archive->close();

        // Delete the temp file
        unlink($output);

        if (!file_exists($unzipPath . '/' . $package->handle . '/controller.php')) {
            throw new InvalidPackageException();
        }

        // Move the files into place
        $packageDir = DIR_PACKAGES . '/' . $package->handle;
        if ($overwrite && file_exists($packageDir)) {
            if (!rename($packageDir, $packageDir . '.old')) {
                throw new UnableToPlacePackageException();
            }
        }

        if (!rename($unzipPath . '/' . $package->handle, $packageDir)) {
            if ($overwrite) {
                rename($packageDir . '.old', $packageDir);
            }
            throw new UnableToPlacePackageException();
        }

        $this->rimraf($package->handle . '.old');
        rmdir($unzipPath);
    }

    protected function rimraf(string $handle)
    {
        // Make sure we're working with a valid dir
        if (!DIR_PACKAGES || !$handle || substr($handle, -4) !== '.old') {
            trigger_error('Invalid handle provided to delete.');
        }

        $path = DIR_PACKAGES . '/' . $handle;

        if (is_file($path)) {
            unlink($path);
        }

        if (is_dir($path)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileInfo) {
                $fileInfo->isDir() ? rmdir($fileInfo->getRealPath()) : unlink($fileInfo->getRealPath());
            }

            rmdir($path);
        }
    }

    public function getConnection(): ?ConnectionInterface
    {
        $dbConfig = $this->app->make('config/database');
        $public = $dbConfig->get('concrete.marketplace.key.public');
        $private = $dbConfig->get('concrete.marketplace.key.private');

        if (!$public || !$private) {
            return null;
        }

        return new Connection($public, $private);
    }

    public function connect(): ConnectionInterface
    {
        $request = $this->requestFor('GET', 'connect');

        try {
            $response = $this->client->send($request, [
                RequestOptions::TIMEOUT => 2,
            ]);
            $contents = $response->getBody()->getContents();
            $data = json_decode($contents, true, 2, JSON_THROW_ON_ERROR);

            if (isset($data['error'])) {
                $error = $this->serializer->denormalize($data, ConnectError::class);
                assert($error instanceof ConnectError);
                throw new UnableToConnectException($error->error, $error->code);
            }

            $result = $this->serializer->denormalize($data, ConnectResult::class);
            assert($result instanceof ConnectResult);
        } catch (BadResponseException|ConnectException $e) {
            throw new UnableToConnectException($e->getMessage(), $e->getCode(), $e);
        } catch (\JsonException|ExceptionInterface $e) {
            throw new InvalidConnectResponseException($contents ?? '');
        }

        $dbConfig = $this->app->make('config/database');
        $dbConfig->save('concrete.marketplace.key', [
            'public' => $result->public,
            'private' => $result->private,
        ]);

        return new Connection($result->public, $result->private);
    }

    public function registerUrl(ConnectionInterface $connection): void
    {
        $request = $this->authenticate($this->requestFor('POST', 'register_url'), $connection);
        $this->client->send($request);
    }

    public function validate(ConnectionInterface $connection, $returnFullObject = false): bool|ValidateResult
    {
        $request = $this->authenticate($this->requestFor('GET', 'connect_validate'), $connection);

        try {
            $response = $this->client->send($request);

            if ($response->getStatusCode() !== 200) {
                if ($returnFullObject) {
                    return new ValidateResult(false, '', '');
                } else {
                    return false;
                }
            }

            $contents = $response->getBody()->getContents();
            $data = json_decode($contents, true, 2, JSON_THROW_ON_ERROR);

            $result = $this->serializer->denormalize($data, ValidateResult::class);
        } catch (BadResponseException|\JsonException|ExceptionInterface $e) {
            if ($returnFullObject) {
                return new ValidateResult(false, '', '', ValidateResult::VALIDATE_RESULT_ERROR);
            } else {
                return false;
            }
        }

        if ($returnFullObject) {
            return $result;
        } else {
            return $result->valid;
        }
    }

    /**
     * @param ConnectionInterface $connection
     * @param UpdatedFieldInterface[] $updatedFields
     * @return void
     */
    public function update(ConnectionInterface $connection, array $updatedFields): void
    {
        $formParams = [];
        foreach ($updatedFields as $field) {
            $formParams[$field->getName()] = $field->getData();
        }
        $request = $this->authenticate($this->requestFor('POST', 'update'), $connection);
        $response = $this->client->post($request->getUri(), ['form_params' => $formParams]);
    }

    protected function authenticate(
        RequestInterface $request,
        ConnectionInterface $connection,
        string $algo = 'sha256'
    ): RequestInterface {
        $now = new \DateTimeImmutable();
        $time = $now->setTime((int) $now->format('h'), (int) $now->format('i'));
        $nonce = $algo . ',' . hash_hmac($algo, (string) $time->getTimestamp(), $connection->getPrivate());

        return $request->withUri($request->getUri()->withUserInfo($connection->getPublic(), $nonce));
    }

    protected function requestFor(string $method, string $pathKey, ...$interpolate): RequestInterface
    {
        $path = $this->paths[$pathKey] ?? '/';
        if (count($interpolate) > 0) {
            $path = sprintf($path, ...$interpolate);
        }

        return $this->addQuery(new Request($method, (new Uri($this->baseUri))->withPath($path)));
    }

    protected function addQuery(RequestInterface $request): RequestInterface
    {
        $site = $this->siteService->getDefault();
        return $request->withUri($request->getUri()->withQuery(http_build_query([
            'csiURL' => $site->getSiteCanonicalURL(),
            'csiName' => $site->getSiteName(),
            'csiVersion' => $this->config->get('concrete.version'),
            'ms' => $this->config->get('concrete.multisite.enabled') ? 1 : 0,
        ])));
    }
}
