<?php

declare(strict_types=1);

namespace Concrete\Tests\Url\Validation;

use Concrete\Core\Url\Url;
use Concrete\Core\Url\Validation\InvalidRemoteUrlException;
use Concrete\Core\Url\Validation\RemoteUrlRequestOptionsBuilder;
use Concrete\Core\Url\Validation\RemoteUrlValidator;
use Concrete\Core\Url\Validation\ValidatedRemoteUrl;
use Concrete\Tests\TestCase;
use GuzzleHttp\RequestOptions;

class RemoteUrlValidationTest extends TestCase
{
    public function testValidateWrapsParsedUrlAndResolvedIp(): void
    {
        $validatedUrl = (new RemoteUrlValidator())->validate('http://8.8.8.8:8080/feed');

        $this->assertInstanceOf(ValidatedRemoteUrl::class, $validatedUrl);
        $this->assertInstanceOf(Url::class, $validatedUrl->getUrl());
        $this->assertSame('http', $validatedUrl->getScheme());
        $this->assertSame('8.8.8.8', $validatedUrl->getHost());
        $this->assertSame(8080, $validatedUrl->getPort());
        $this->assertSame('8.8.8.8', $validatedUrl->getIp());
        $this->assertSame('http://8.8.8.8:8080/feed', (string) $validatedUrl);
    }

    public function testBuildUsesWrappedUrlComponentsForCurlResolve(): void
    {
        $validatedUrl = (new RemoteUrlValidator())->validate('https://8.8.8.8/feed');
        $options = (new RemoteUrlRequestOptionsBuilder())->build($validatedUrl);

        $this->assertFalse($options[RequestOptions::ALLOW_REDIRECTS]);
        $this->assertSame(
            ['8.8.8.8:443:8.8.8.8'],
            $options['curl'][CURLOPT_RESOLVE]
        );
    }

    public function testValidateRejectsLocalhost(): void
    {
        $this->expectException(InvalidRemoteUrlException::class);
        $this->expectExceptionMessage('Invalid URL host.');

        (new RemoteUrlValidator())->validate('http://localhost/feed');
    }
}
