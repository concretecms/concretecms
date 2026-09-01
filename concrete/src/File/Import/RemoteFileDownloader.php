<?php

declare(strict_types=1);

namespace Concrete\Core\File\Import;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\Service\Mime;
use Concrete\Core\File\ValidationService;
use Concrete\Core\Http\Client\Client;
use Concrete\Core\Url\Validation\InvalidRemoteUrlException;
use Concrete\Core\Url\Validation\RemoteUrlRequestOptionsBuilder;
use Concrete\Core\Url\Validation\RemoteUrlValidator;
use Concrete\Core\Url\Validation\ValidatedRemoteUrl;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Download a remote file, so that it can be imported into the file manager.
 */
class RemoteFileDownloader
{
    /**
     * @var \Concrete\Core\Http\Client\Client
     */
    protected $client;

    /**
     * @var \Concrete\Core\Url\Validation\RemoteUrlValidator
     */
    protected $urlValidator;

    /**
     * @var \Concrete\Core\Url\Validation\RemoteUrlRequestOptionsBuilder
     */
    protected $requestOptionsBuilder;

    /**
     * @var \Concrete\Core\File\ValidationService
     */
    protected $fileValidator;

    /**
     * @var \Concrete\Core\File\Service\Mime
     */
    protected $mimeHelper;

    public function __construct(
        Client $client,
        RemoteUrlValidator $urlValidator,
        RemoteUrlRequestOptionsBuilder $requestOptionsBuilder,
        ValidationService $fileValidator,
        Mime $mimeHelper
    ) {
        $this->client = $client;
        $this->urlValidator = $urlValidator;
        $this->requestOptionsBuilder = $requestOptionsBuilder;
        $this->fileValidator = $fileValidator;
        $this->mimeHelper = $mimeHelper;
    }

    /**
     * Check that an URL can be downloaded from.
     *
     * @throws \Concrete\Core\Error\UserMessageException if the URL is not valid
     */
    public function validateUrl(string $url): ValidatedRemoteUrl
    {
        try {
            return $this->urlValidator->validate($url);
        } catch (InvalidRemoteUrlException $x) {
            if ($x->getPrevious() !== null) {
                throw new UserMessageException(t('The URL "%s" is not valid: %s', $url, $x->getMessage()));
            }
            throw new UserMessageException(t('The URL "%s" is not valid.', $url));
        }
    }

    /**
     * Download a remote file into a directory, and return the full path of the downloaded file.
     *
     * The name of the file is taken from the URL, or from the mime type declared by the remote server; in
     * both the cases the resulting extension must be one that this installation accepts.
     *
     * @param \Concrete\Core\Url\Validation\ValidatedRemoteUrl|null $validatedUrl the already validated URL (it's validated here when not specified)
     * @param string $filename the name the file must have (when empty, it's determined out of the URL or of the mime type)
     *
     * @throws \Concrete\Core\Error\UserMessageException if the URL is not valid, if it can't be downloaded, or if the file can't be accepted
     */
    public function download(string $url, string $temporaryDirectory, ?ValidatedRemoteUrl $validatedUrl = null, string $filename = ''): string
    {
        if ($validatedUrl === null) {
            $validatedUrl = $this->validateUrl($url);
        }
        $request = new Request('GET', $url);
        $response = $this->client->send($request, $this->requestOptionsBuilder->build($validatedUrl));
        if ($response->getStatusCode() !== 200) {
            throw new UserMessageException(t(/* i18n: %1$s is an URL, %2$s is an error message */ 'There was an error downloading "%1$s": %2$s', $url, $response->getReasonPhrase() . ' (' . $response->getStatusCode() . ')'));
        }
        $filename = $this->getFilename($url, $response, $filename);
        $fullFilename = rtrim($temporaryDirectory, DIRECTORY_SEPARATOR . '/') . '/' . $filename;
        if (@file_put_contents($fullFilename, $response->getBody()) === false) {
            throw new UserMessageException(t('Failed to save the downloaded file.'));
        }

        return $fullFilename;
    }

    /**
     * Determine the name of the file downloaded from an URL, checking that this installation accepts it.
     *
     * @param string $filename the name the file must have (when empty, it's determined out of the URL or of the mime type)
     *
     * @throws \Concrete\Core\Error\UserMessageException if the name can't be determined, or if it can't be accepted
     */
    public function getFilename(string $url, ResponseInterface $response, string $filename = ''): string
    {
        if ($filename !== '') {
            return $this->checkFilename($filename);
        }
        $matches = null;
        if (preg_match('/^[^#\?]+[\/]([-\w%]+\.[-\w%]+)($|\?|#)/', $url, $matches)) {
            // the URL contains a file name (with its extension): use it
            return $this->checkFilename($matches[1]);
        }
        foreach ($response->getHeader('Content-Type') as $contentType) {
            if ($contentType === '') {
                continue;
            }
            [$mimeType] = explode(';', $contentType, 2);
            $mimeType = trim($mimeType);
            $extension = $this->mimeHelper->mimeToExtension($mimeType);
            if ($extension === false) {
                throw new UserMessageException(t('Unknown mime-type: %s', $mimeType));
            }

            return $this->checkFilename(date('Y-m-d_H-i_') . mt_rand(100, 999) . '.' . $extension);
        }

        throw new UserMessageException(t(/* i18n: %s is an URL */ 'Could not determine the name of the file at %s', $url));
    }

    /**
     * Check that a file name can be used: it must not contain a path, and this installation must accept
     * files with its extension.
     *
     * @throws \Concrete\Core\Error\UserMessageException if the name can't be accepted
     */
    protected function checkFilename(string $filename): string
    {
        $filename = trim(basename(str_replace('\\', '/', $filename)));
        if ($filename === '' || trim($filename, '.') === '') {
            throw new UserMessageException(t('The name of the file is not valid.'));
        }
        if (!$this->fileValidator->extension($filename)) {
            throw new UserMessageException(t('The file extension "%s" is not valid.', pathinfo($filename, PATHINFO_EXTENSION)));
        }

        return $filename;
    }
}

