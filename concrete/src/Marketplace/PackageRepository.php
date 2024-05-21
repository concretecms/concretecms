<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace;

use Concrete\Core\Marketplace\Exception\ErrorSavingRemoteDataException;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\File\Service\File;
use Concrete\Core\Marketplace\Exception\InvalidConnectResponseException;
use Concrete\Core\Marketplace\Exception\InvalidDownloadResponseException;
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
use Concrete\Core\Url\Url;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;

final class PackageRepository implements PackageRepositoryInterface
{
    /** @var Client */
    private $client;
    /** @var Serializer */
    private $serializer;
    /** @var Repository  */
    private $config;
    /** @var Repository  */
    private $databaseConfig;
    /** @var Service */
    private $siteService;
    /** @var string */
    private $baseUri;
    /** @var array<string, string> */
    private $paths;
    /** @var File */
    private $fileHelper;

    public function __construct(
        Client $client,
        Serializer $serializer,
        Repository $config,
        Repository $databaseConfig,
        Service $siteService,
        File $fileHelper,
        string $baseUri,
        array $paths
    ) {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->config = $config;
        $this->databaseConfig = $databaseConfig;
        $this->siteService = $siteService;
        $this->fileHelper = $fileHelper;
        $this->baseUri = $baseUri;
        $this->paths = $paths;
    }

    public function getPackage(ConnectionInterface $connection, string $packageId): ?RemotePackage
    {
        try {
            $request = $this->authenticate($this->requestFor('GET', 'get', $packageId), $connection);
            $response = $this->client->send($request);
            $data = json_decode($response->getBody()->getContents(), true, 4, JSON_THROW_ON_ERROR);
            $result = $this->serializer->denormalize($data, RemotePackage::class);
        } catch (BadResponseException|\JsonException|ExceptionInterface|ConnectException $e) {
            return null;
        }

        return $result;
    }

    public function getPackages(ConnectionInterface $connection, bool $latestOnly = false, bool $compatibleOnly = false): array
    {
        try {
            $request = $this->authenticate($this->requestFor('GET', 'list'), $connection);
            $response = $this->client->send($request);
            $data = json_decode($response->getBody()->getContents(), true, 4, JSON_THROW_ON_ERROR);
            /** @var RemotePackage[] $result */
            $result = array_map(function ($item) {
                return $this->serializer->denormalize($item, RemotePackage::class);
            }, $data);
        } catch (BadResponseException|\JsonException|ExceptionInterface|ConnectException $e) {
            return [];
        }

        if ($compatibleOnly) {
            $me = $this->config->get('concrete.version');
            $result = array_filter($result, function ($item) use ($me) {
                return in_array($me, $item->compatibility, true);
            });
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
        try {
            $request = $this->authenticate(new Request('GET', new Uri($package->download)), $connection);
            $output = tempnam($this->fileHelper->getTemporaryDirectory(), $package->handle);
            $this->client->send($request, [
                RequestOptions::ALLOW_REDIRECTS => true,
                RequestOptions::SINK => $output,
            ]);
        } catch (ClientException $e) {
            throw new InvalidDownloadResponseException($e->getMessage());
        }

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
            if (!$this->rename($packageDir, $packageDir . '.old')) {
                throw new UnableToPlacePackageException();
            }
        }

        if (!$this->rename($unzipPath . '/' . $package->handle, $packageDir)) {
            if ($overwrite) {
                $this->rename($packageDir . '.old', $packageDir);
            }
            throw new UnableToPlacePackageException();
        }

        $this->rimraf($package->handle . '.old');
        rmdir($unzipPath);
    }

    protected function rename(string $old, string $new): bool
    {
        // First try calling rename, this is the most efficient way to rename things but unfortunately fails if old is a
        // directory and on a different filesystem than new.
        if (@rename($old, $new)) {
            return true;
        }

        // Fallback to copying to new then deleting old
        $this->fileHelper->copyAll($old, $new);
        if (!file_exists($new)) {
            return false;
        }

        // Remove the old directory
        $this->fileHelper->removeAll($old, true);
        return true;
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
        $public = $this->databaseConfig->get('concrete.marketplace.key.public');
        $private = $this->databaseConfig->get('concrete.marketplace.key.private');

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

        $this->databaseConfig->save('concrete.marketplace.key', [
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

    /**
     * @return bool|ValidateResult
     */
    public function validate(ConnectionInterface $connection, bool $returnFullObject = false)
    {
        try {
            $request = $this->authenticate($this->requestFor('GET', 'connect_validate'), $connection);
            $response = $this->client->send($request);

            if ($response->getStatusCode() !== 200) {
                if ($returnFullObject) {
                    return new ValidateResult(false, '', '');
                }

                return false;
            }

            $contents = $response->getBody()->getContents();
            $data = json_decode($contents, true, 2, JSON_THROW_ON_ERROR);

            $result = $this->serializer->denormalize($data, ValidateResult::class);
        } catch (BadResponseException|\JsonException|ExceptionInterface|ConnectException $e) {
            if ($returnFullObject) {
                return new ValidateResult(false, '', '', ValidateResult::VALIDATE_RESULT_ERROR);
            }

            return false;
        }

        if ($returnFullObject) {
            return $result;
        }

        return $result->valid;
    }

    /**
     * @param ConnectionInterface $connection
     * @param UpdatedFieldInterface[] $updatedFields
     * @return void
     */
    public function update(ConnectionInterface $connection, array $updatedFields): void
    {
        try {
            $formParams = [];
            foreach ($updatedFields as $field) {
                $formParams[$field->getName()] = $field->getData();
            }
            $request = $this->authenticate($this->requestFor('POST', 'update'), $connection);
            $response = $this->client->post($request->getUri(), ['form_params' => $formParams]);
        } catch (\Exception $e) {
            // https://github.com/concretecms/concretecms/issues/12079
            // We don't want to be noisy _every_ when doing these updates.
            throw new ErrorSavingRemoteDataException($e->getMessage());
        }
    }

    protected function authenticate(
        RequestInterface $request,
        ConnectionInterface $connection,
        string $algo = 'sha256'
    ): RequestInterface {
        $now = new \DateTimeImmutable('', new \DateTimeZone('UTC'));
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

    private function getUrl(Site $site): string
    {
        $url = (string) $site->getSiteCanonicalURL();
        if ($url === '') {
            try {
                $urlFromServer = Url::createFromServer($_SERVER);
                $url = $urlFromServer->getBaseUrl();
            } catch (\Exception $e) {

            }
        }
        return $url;
    }
    protected function addQuery(RequestInterface $request): RequestInterface
    {
        $site = $this->siteService->getDefault();
        return $request->withUri($request->getUri()->withQuery(http_build_query([
            'csiURL' => $site ? $this->getUrl($site) : null,
            'csiName' => $site ? $site->getSiteName() : null,
            'csiVersion' => $this->config->get('concrete.version'),
            'ms' => $this->config->get('concrete.multisite.enabled') ? 1 : 0,
        ])));
    }
}
