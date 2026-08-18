<?php

declare(strict_types=1);

namespace Concrete\Tests\File\Import;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\Import\RemoteFileDownloader;
use Concrete\Tests\TestCase;
use GuzzleHttp\Psr7\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class RemoteFileDownloaderTest extends TestCase
{
    private function getDownloader(): RemoteFileDownloader
    {
        return app(RemoteFileDownloader::class);
    }

    public static function providerFilenamesFromUrl(): array
    {
        return [
            'plain URL' => ['https://www.example.com/path/to/image.png', 'image.png'],
            'URL with a query string' => ['https://www.example.com/image.png?v=3', 'image.png'],
            'URL with a fragment' => ['https://www.example.com/image.png#anchor', 'image.png'],
        ];
    }

    /**
     * @dataProvider providerFilenamesFromUrl
     */
    public function testTheFilenameIsTakenFromTheUrl(string $url, string $expected): void
    {
        static::assertSame($expected, $this->getDownloader()->getFilename($url, new Response(200)));
    }

    public function testTheFilenameFallsBackToTheMimeType(): void
    {
        $response = new Response(200, ['Content-Type' => 'image/png; charset=binary']);

        $filename = $this->getDownloader()->getFilename('https://www.example.com/download', $response);

        static::assertMatchesRegularExpression('/^[\d\-_]+\d{3}\.png$/', $filename);
    }

    public function testUnknownMimeTypesAreRejected(): void
    {
        $response = new Response(200, ['Content-Type' => 'application/x-what-is-this']);

        $this->expectException(UserMessageException::class);
        $this->getDownloader()->getFilename('https://www.example.com/download', $response);
    }

    public function testTheFilenameMustBeDeterminable(): void
    {
        $this->expectException(UserMessageException::class);
        $this->getDownloader()->getFilename('https://www.example.com/download', new Response(200));
    }

    public static function providerInvalidUrls(): array
    {
        return [
            'not an URL' => ['this is not an URL'],
            'unsupported scheme' => ['file:///etc/passwd'],
            'no host' => ['https:///path'],
            'localhost' => ['http://localhost/image.png'],
            'loopback address' => ['http://127.0.0.1/image.png'],
            'private network' => ['http://192.168.1.1/image.png'],
            'decimal notation' => ['http://2130706433/image.png'],
        ];
    }

    /**
     * @dataProvider providerInvalidUrls
     */
    public function testUrlsThatCantBeDownloadedFrom(string $url): void
    {
        $this->expectException(UserMessageException::class);
        $this->getDownloader()->validateUrl($url);
    }
}
