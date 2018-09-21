<?php

namespace Concrete\Core\Http\PSR7;

use GuzzleHttp\Psr7\Response as PsrResponse;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\UploadedFile as PsrUploadedFile;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Guzzle HTTP message factory. Credits to https://github.com/Majkl578/symfony-guzzle-psr7-bridge
 */
class GuzzleFactory implements HttpMessageFactoryInterface
{

    const DEFAULT_PROTOCOL_VERSION = '1.1';

    public function createRequest(Request $symfonyRequest)
    {
        $psrRequest = new ServerRequest(
            $symfonyRequest->getMethod(),
            new Uri($symfonyRequest->getUri()),
            $symfonyRequest->headers->all(),
            new Stream($symfonyRequest->getContent(true)),
            $this->detectRequestProtocol($symfonyRequest),
            $symfonyRequest->server->all()
        );

        $psrRequest = $psrRequest
            ->withUploadedFiles($this->createUploadedFiles($symfonyRequest))
            ->withCookieParams($symfonyRequest->cookies->all())
            ->withQueryParams($symfonyRequest->query->all())
            ->withParsedBody($symfonyRequest->request->all());

        foreach ($symfonyRequest->attributes->all() as $key => $value) {
            $psrRequest = $psrRequest->withAttribute($key, $value);
        }

        return $psrRequest;
    }

    public function createResponse(Response $symfonyResponse)
    {
        return new PsrResponse(
            $symfonyResponse->getStatusCode(),
            $this->createResponseHeaders($symfonyResponse),
            $this->createResponseStream($symfonyResponse),
            $symfonyResponse->getProtocolVersion()
        );
    }

    private function detectRequestProtocol(Request $symfonyRequest)
    {
        if (!$symfonyRequest->server->has('SERVER_PROTOCOL')) {
            return self::DEFAULT_PROTOCOL_VERSION;
        }

        if (!preg_match('~^HTTP/(?<version>\d(?:\.\d)?)\z~', $symfonyRequest->server->get('SERVER_PROTOCOL'), $match)) {
            // ignore malformed protocol version
            return self::DEFAULT_PROTOCOL_VERSION;
        }

        return $match['version'];
    }

    /**
     * @return UploadedFileInterface[]
     */
    private function createUploadedFiles(Request $symfonyRequest)
    {
        $files = [];

        foreach ($symfonyRequest->files as $file) {
            $files[] = $this->createUploadedFile($file);
        }

        return $files;
    }

    private function createUploadedFile(UploadedFile $symfonyUploadedFile)
    {
        return new PsrUploadedFile(
            $symfonyUploadedFile->getRealPath(),
            $symfonyUploadedFile->getClientSize(),
            $symfonyUploadedFile->getError(),
            $symfonyUploadedFile->getClientOriginalName(),
            $symfonyUploadedFile->getClientMimeType()
        );
    }

    /**
     * @return string[][]
     */
    private function createResponseHeaders(Response $symfonyResponse)
    {
        $headers = $symfonyResponse->headers->allPreserveCase();
        $cookies = $symfonyResponse->headers->getCookies();

        if (empty($cookies)) {
            return $headers;
        }

        $headers['Set-Cookie'] = [];
        foreach ($cookies as $cookie) {
            /** @var Cookie $cookie */
            $headers['Set-Cookie'][] = $cookie->__toString();
        }

        return $headers;
    }

    private function createResponseStream(Response $symfonyResponse)
    {
        if ($symfonyResponse instanceof BinaryFileResponse) {
            return $this->createStreamFromBinaryFileResponse($symfonyResponse);
        }

        if ($symfonyResponse instanceof StreamedResponse) {
            return $this->createStreamFromStreamedResponse($symfonyResponse);
        }

        return $this->createStreamFromResponseContent($symfonyResponse);
    }

    private function createStreamFromBinaryFileResponse(BinaryFileResponse $symfonyResponse)
    {
        $handle = @fopen($symfonyResponse->getFile()->getPathname(), 'r');
        assert($handle !== false);

        return new Stream($handle);
    }

    private function createStreamFromStreamedResponse(StreamedResponse $symfonyResponse)
    {
        $handle = fopen('php://temp', 'r+');
        assert($handle !== false);

        $stream = new Stream($handle);

        ob_start(function ($output) use ($stream) {
            $stream->write($output);
            return '';
        });

        $symfonyResponse->sendContent();

        ob_end_clean();

        $stream->rewind();

        return $stream;
    }

    private function createStreamFromResponseContent(Response $symfonyResponse)
    {
        /** @var string|bool $content */
        $content = $symfonyResponse->getContent();
        assert($content !== false);

        $handle = fopen('php://temp', 'r+');
        assert($handle !== false);

        $stream = new Stream($handle);
        $stream->write($content);
        $stream->rewind();

        return $stream;
    }

}
