<?php

declare(strict_types=1);

namespace Concrete\Tests\Http;

use Concrete\Core\Http\Traits\AcceptsJsonTrait;
use Concrete\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

defined('C5_EXECUTE') or die('Access Denied.');

class AcceptsJsonTraitTest extends TestCase
{
    use AcceptsJsonTrait;

    /**
     * @dataProvider provideRequests
     */
    public function testWhatARequestExpects(?string $accept, bool $xmlHttpRequest, bool $expects, bool $accepts): void
    {
        static::assertSame($expects, static::requestExpectsJson($this->buildRequest($accept, $xmlHttpRequest)));
    }

    /**
     * @dataProvider provideRequests
     */
    public function testWhatARequestAccepts(?string $accept, bool $xmlHttpRequest, bool $expects, bool $accepts): void
    {
        static::assertSame($accepts, static::requestAcceptsJson($this->buildRequest($accept, $xmlHttpRequest)));
    }

    /**
     * The requests the two methods are checked against. Every case holds:
     *
     * - the Accept header of the request (NULL when it has none)
     * - whether the request has been made with XMLHttpRequest
     * - whether the request expects a JSON response
     * - whether the request can be answered with JSON
     *
     * @return array<string,array{0: string|null, 1: bool, 2: bool, 3: bool}>
     */
    public static function provideRequests(): array
    {
        return [
            'no Accept header at all' => [
                null,
                false,
                false,
                false,
            ],
            'a browser' => [
                'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                false,
                false,
                false,
            ],
            'anything goes' => [
                '*/*',
                false,
                false,
                false,
            ],
            'a REST client' => [
                'application/json',
                false,
                true,
                true,
            ],
            'axios' => [
                'application/json, text/plain, */*',
                false,
                true,
                true,
            ],
            'jQuery asking for JSON' => [
                'application/json, text/javascript, */*; q=0.01',
                false,
                true,
                true,
            ],
            'JSON preferred over HTML' => [
                'text/html;q=0.5, application/json;q=0.9',
                false,
                true,
                true,
            ],
            'HTML preferred over JSON' => [
                'application/json;q=0.5, text/html;q=0.9',
                false,
                false,
                false,
            ],
            'JSON-LD' => [
                'application/ld+json',
                false,
                true,
                true,
            ],
            'the problem details of RFC 9457' => [
                'application/problem+json',
                false,
                true,
                true,
            ],
            'JSON:API' => [
                'application/vnd.api+json',
                false,
                true,
                true,
            ],
            'XML' => [
                'application/xml',
                false,
                false,
                false,
            ],
            'SVG, whose syntax is XML' => [
                'image/svg+xml',
                false,
                false,
                false,
            ],
            'an XHR call' => [
                null,
                true,
                false,
                true,
            ],
            'an XHR call from a page' => [
                'text/html,*/*;q=0.8',
                true,
                false,
                true,
            ],
            'an XHR call asking for JSON' => [
                'application/json',
                true,
                true,
                true,
            ],
        ];
    }

    private function buildRequest(?string $accept, bool $xmlHttpRequest): Request
    {
        $server = [];
        if ($accept !== null) {
            $server['HTTP_ACCEPT'] = $accept;
        }
        if ($xmlHttpRequest) {
            $server['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        }

        return Request::create('/ccm/api/1.0/pages', 'GET', [], [], [], $server);
    }
}
