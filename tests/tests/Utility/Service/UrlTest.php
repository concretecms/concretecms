<?php

namespace Concrete\Tests\Utility\Service;
class UrlTest extends \PHPUnit_Framework_TestCase
{

    /** @var array */
    private $serverBackup;

    protected function setUp()
    {
        parent::setUp();
        // Backup and seed $_SERVER
        $this->serverBackup = $_SERVER;
        $_SERVER['REQUEST_URI'] = '/list"items\'here?existing=1';
        $_SERVER['QUERY_STRING'] = 'foo="bar"&baz=\'qux\'';
    }

    protected function tearDown()
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
    public function testUsesRequestUriWhenUrlIsFalseAndEncodesQuotes()
    {
        $uh = $this->urlHelper();

        $out = $uh->setVariable([], false, false);

        // REQUEST_URI contained both " and ' → must be percent-encoded
        $this->assertContains('/list%22items%27here', $out, 'Base path quotes must be encoded.');
        // Ensure no CR/LF made it through
        $this->assertNotContains("\r", $out);
        $this->assertNotContains("\n", $out);
    }

    /**
     * When base URL has no '?', it must:
     *  - encode quotes in the base URL
     *  - sanitize and encode quotes in the query string before appending
     */
    public function testBaseUrlWithoutQueryGetsSanitizedQueryAppendedWithEncodedQuotes()
    {
        $uh = $this->urlHelper();

        $_SERVER['QUERY_STRING'] = 'a="1"&b=\'2\''; // quotes to be encoded

        $base = "/products/rock'n\"roll"; // both quotes in base URL
        $out  = $uh->setVariable([], false, $base);

        // Base URL quotes encoded
        $this->assertContains('/products/rock%27n%22roll', $out);

        // Query string appended and quotes encoded
        $this->assertContains('?a=%221%22&b=%272%27', $out);

        // No CR/LF
        $this->assertNotContains("\r", $out);
        $this->assertNotContains("\n", $out);
    }

    /**
     * It should not double-encode when the output is fed back into setVariable() again.
     */
    public function testReentryDoesNotDoubleEncode()
    {
        $uh = $this->urlHelper();

        $_SERVER['QUERY_STRING'] = 'x="y"';

        $base = '/path"quote\'apostrophe';
        $first = $uh->setVariable(['p' => 'v'], false, $base);
        // Feed the result back in (simulating re-entry)
        $second = $uh->setVariable(['p2' => 'v2'], false, $first);

        // Quotes should appear encoded once, not as %2522 / %2527
        $this->assertContains('/path%22quote%27apostrophe', $second);
        $this->assertNotContains('%2522', $second, 'No double-encoding of %22.');
        $this->assertNotContains('%2527', $second, 'No double-encoding of %27.');

        // Both parameter sets present
        $this->assertContains('p=v', $second);
        $this->assertContains('p2=v2', $second);
        $this->assertContains('x=%22y%22', $second);
    }

    /**
     * If there is already a '?', the elseif branch does not run; verify we still
     * retain existing query and that added variables merge correctly (no duplicate encoding).
     */
    public function testExistingQueryIsPreservedAndMergedWithoutReencoding()
    {
        $uh = $this->urlHelper();

        $in = '/search?q=rock%27n%22roll'; // already percent-encoded quotes
        $out = $uh->setVariable(['page' => '1'], false, $in);

        // Existing encoding should not change (no %2527 / %2522)
        $this->assertContains('q=rock%27n%22roll', $out);
        $this->assertNotContains('%2527', $out);
        $this->assertNotContains('%2522', $out);

        // New param merged
        $this->assertContains('page=1', $out);
    }

    /**
     * Control characters in QUERY_STRING should be removed.
     */
    public function testControlCharsAreStripped()
    {
        $uh = $this->urlHelper();

        $_SERVER['QUERY_STRING'] = "a=1\r\nb=2\"c'3";
        $out = $uh->setVariable([], false, '/x');

        $this->assertNotContains("\r", $out);
        $this->assertNotContains("\n", $out);
        // Quotes encoded from the query string portion
        $this->assertContains('b=2%22c%273', $out);
    }

    public function testSimpleUrlsBehaveNormally()
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
        $this->assertContains('/search?q=test', $out3, 'Existing query must be preserved.');
        $this->assertContains('page=1', $out3, 'New parameter should be merged in.');
        $this->assertNotContains('%2520', $out3, 'No double-encoding of percent sequences.');
    }

}
