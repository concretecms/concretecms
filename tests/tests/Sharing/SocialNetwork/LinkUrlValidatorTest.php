<?php

declare(strict_types=1);

namespace Concrete\Tests\Sharing\SocialNetwork;

use Concrete\Core\Sharing\SocialNetwork\LinkUrlValidator;
use Concrete\Tests\TestCase;

class LinkUrlValidatorTest extends TestCase
{
    public static function provideCases(): array
    {
        return [
            ['https://www.facebook.com/concretecms', true],
            ['http://example.com/path?a=1&b=2#x', true],
            ['HTTPS://EXAMPLE.COM', true],
            ['mailto:john@example.com', true],
            ['mailto:john@example.com?subject=Hello', true],
            ['tel:+41123456789', true],
            ['tel:+1-(555)-123.4567', true],
            ['tel:5551234;ext=12', true],
            ['', false],
            ['example.com', false],
            ['//example.com', false],
            ['http://', false],
            ['http:/example.com', false],
            ['mailto:', false],
            ['mailto:not an email', false],
            ['mailto:<script>@example.com', false],
            ['tel:', false],
            ['tel:+41 12 34', false],
            ['tel:call-me', false],
            ['javascript:alert(1)', false],
            ['javascript://%0aalert(1)', false],
            ["javascript://\nalert(1)", false],
            ['JaVaScRiPt://example.com', false],
            ['data:text/html,x', false],
            ['vbscript:x', false],
            ["https://example.com/\n", false],
            [' https://example.com', false],
            [null, false],
            [['https://example.com'], false],
        ];
    }

    /**
     * @dataProvider provideCases
     *
     * @param mixed $url
     */
    public function testIsValid($url, bool $expected): void
    {
        static::assertSame($expected, (new LinkUrlValidator())->isValid($url));
    }

    public static function provideAdditionalSchemeCases(): array
    {
        return [
            ['xmpp:john@example.com?message', true],
            ['XMPP:john@example.com', true],
            ["xmpp:john\n@example.com", false],
            ['sms:+41123456', false],
            ['javascript:alert(1)', false],
            ['data:text/html,x', false],
        ];
    }

    /**
     * @dataProvider provideAdditionalSchemeCases
     */
    public function testAdditionalSchemes(string $url, bool $expected): void
    {
        $validator = new LinkUrlValidator(['xmpp', 'JavaScript', ' data ', 'not a scheme']);
        static::assertSame(['http', 'https', 'mailto', 'tel', 'xmpp'], $validator->getAllowedSchemes());
        static::assertSame($expected, $validator->isValid($url));
    }
}
