<?php

declare(strict_types=1);

namespace Concrete\Tests\Utility\Service;

use Concrete\Tests\TestCase;

class UrlTest extends TestCase
{

    /** @var array */
    private $serverBackup;

    protected function setUp(): void
    {
        parent::setUp();
        // Backup and seed $_SERVER
        $this->serverBackup = $_SERVER;
        $_SERVER['REQUEST_URI'] = '/list"items\'here?existing=1';
        $_SERVER['QUERY_STRING'] = 'foo="bar"&baz=\'qux\'';
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
        parent::tearDown();
    }

    /**
     * Helper: get the URL helper under test.
     */
    private function urlHelper()
    {
        // If your test bootstrap wires \Core::make, use that. Otherwise, new up your helper class directly.
        return \Core::make('helper/url');
    }

    /**
     * It should use REQUEST_URI when $url == false and encode quotes / strip CRLF.
     */
    public function testUsesRequestUriWhenUrlIsFalseAndEncodesQuotes(): void
    {
        $uh = $this->urlHelper();

        $out = $uh->setVariable([], false, false);

        // REQUEST_URI contained both " and ' → must be percent-encoded
        $this->assertStringContainsString('/list%22items%27here', $out, 'Base path quotes must be encoded.');
        // Ensure no CR/LF made it through
        $this->assertStringNotContainsString("\r", $out);
        $this->assertStringNotContainsString("\n", $out);
    }

    /**
     * When base URL has no '?', it must:
     *  - encode quotes in the base URL
     *  - sanitize and encode quotes in the query string before appending
     */
    public function testBaseUrlWithoutQueryGetsSanitizedQueryAppendedWithEncodedQuotes(): void
    {
        $uh = $this->urlHelper();

        $_SERVER['QUERY_STRING'] = 'a="1"&b=\'2\''; // quotes to be encoded

        $base = "/products/rock'n\"roll"; // both quotes in base URL
        $out  = $uh->setVariable([], false, $base);

        // Base URL quotes encoded
        $this->assertStringContainsString('/products/rock%27n%22roll', $out);

        // Query string appended and quotes encoded
        $this->assertStringContainsString('?a=%221%22&b=%272%27', $out);

        // No CR/LF
        $this->assertStringNotContainsString("\r", $out);
        $this->assertStringNotContainsString("\n", $out);
    }

    /**
     * It should not double-encode when the output is fed back into setVariable() again.
     */
    public function testReentryDoesNotDoubleEncode(): void
    {
        $uh = $this->urlHelper();

        $_SERVER['QUERY_STRING'] = 'x="y"';

        $base = '/path"quote\'apostrophe';
        $first = $uh->setVariable(['p' => 'v'], false, $base);
        // Feed the result back in (simulating re-entry)
        $second = $uh->setVariable(['p2' => 'v2'], false, $first);

        // Quotes should appear encoded once, not as %2522 / %2527
        $this->assertStringContainsString('/path%22quote%27apostrophe', $second);
        $this->assertStringNotContainsString('%2522', $second, 'No double-encoding of %22.');
        $this->assertStringNotContainsString('%2527', $second, 'No double-encoding of %27.');

        // Both parameter sets present
        $this->assertStringContainsString('p=v', $second);
        $this->assertStringContainsString('p2=v2', $second);
        $this->assertStringContainsString('x=%22y%22', $second);
    }

    /**
     * If there is already a '?', the elseif branch does not run; verify we still
     * retain existing query and that added variables merge correctly (no duplicate encoding).
     */
    public function testExistingQueryIsPreservedAndMergedWithoutReencoding(): void
    {
        $uh = $this->urlHelper();

        $in = '/search?q=rock%27n%22roll'; // already percent-encoded quotes
        $out = $uh->setVariable(['page' => '1'], false, $in);

        // Existing encoding should not change (no %2527 / %2522)
        $this->assertStringContainsString('q=rock%27n%22roll', $out);
        $this->assertStringNotContainsString('%2527', $out);
        $this->assertStringNotContainsString('%2522', $out);

        // New param merged
        $this->assertStringContainsString('page=1', $out);
    }

    /**
     * Control characters in QUERY_STRING should be removed.
     */
    public function testControlCharsAreStripped(): void
    {
        $uh = $this->urlHelper();

        $_SERVER['QUERY_STRING'] = "a=1\r\nb=2\"c'3";
        $out = $uh->setVariable([], false, '/x');

        $this->assertStringNotContainsString("\r", $out);
        $this->assertStringNotContainsString("\n", $out);
        // Quotes encoded from the query string portion
        $this->assertStringContainsString('b=2%22c%273', $out);
    }

    public function testSimpleUrlsBehaveNormally(): void
    {
        $uh = $this->urlHelper();

        // 1) When $url == false, we use REQUEST_URI as-is (no quotes to encode)
        $_SERVER['REQUEST_URI'] = '/about';
        $_SERVER['QUERY_STRING'] = '';
        $out1 = $uh->setVariable([], false, false);
        $this->assertSame('/about', $out1, 'Plain REQUEST_URI should pass through unchanged.');

        // 2) Base URL without "?" picks up QUERY_STRING as-is (ampersands retained; no HTML escaping here)
        $_SERVER['QUERY_STRING'] = 'page=2&sort=name';
        $out2 = $uh->setVariable([], false, '/shop');
        $this->assertSame('/shop?page=2&sort=name', $out2, 'Query string should be appended normally.');

        // 3) Base URL with existing query keeps it; added vars merge in without re-encoding
        $in   = '/search?q=test';
        $out3 = $uh->setVariable(['page' => '1'], false, $in);
        $this->assertStringContainsString('/search?q=test', $out3, 'Existing query must be preserved.');
        $this->assertStringContainsString('page=1', $out3, 'New parameter should be merged in.');
        $this->assertStringNotContainsString('%2520', $out3, 'No double-encoding of percent sequences.');
    }

}
