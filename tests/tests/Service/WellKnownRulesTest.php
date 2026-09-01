<?php

declare(strict_types=1);

namespace Concrete\Tests\Service;

use Concrete\Core\Service\Configuration\HTTP\ApacheGenerator;
use Concrete\Core\Service\Configuration\HTTP\NginxGenerator;
use Concrete\Tests\TestCase;

class WellKnownRulesTest extends TestCase
{
    // -------------------------------------------------------------------------
    // ApacheGenerator — well_known_files rule
    // -------------------------------------------------------------------------

    public function testApacheRuleBlocksDirectAccessToSiteSpecificDirectory(): void
    {
        $rule = (new ApacheGenerator())->getRule('well_known_files');
        $this->assertNotNull($rule);
        $code = $rule->getCode();
        $this->assertStringContainsString('RewriteRule ^application/files/site-specific/', $code);
        $this->assertStringContainsString('[F,L]', $code);
    }

    public function testApacheRuleValidatesHostHeaderBeforeUsingItAsPath(): void
    {
        $rule = (new ApacheGenerator())->getRule('well_known_files');
        $this->assertNotNull($rule);
        $code = $rule->getCode();
        // Regex must anchor both ends and only allow RFC 1123 hostname characters.
        $this->assertStringContainsString('^([a-zA-Z0-9][a-zA-Z0-9.\\-]*[a-zA-Z0-9])', $code);
    }

    public function testApacheRuleUsesFilenameCapture_NotRequestUri_InCondition(): void
    {
        $rule = (new ApacheGenerator())->getRule('well_known_files');
        $this->assertNotNull($rule);
        $code = $rule->getCode();
        // The file-existence condition must use $1 (the RewriteRule filename capture),
        // not %{REQUEST_URI} which carries a leading slash and produces a double-slash.
        $this->assertStringContainsString('%{DOCUMENT_ROOT}/application/files/site-specific/%1/$1 -f', $code);
        $this->assertStringNotContainsString('%{REQUEST_URI}', $code);
    }

    public function testApacheRuleRoutesAllowedFilenames(): void
    {
        $rule = (new ApacheGenerator())->getRule('well_known_files');
        $this->assertNotNull($rule);
        $code = $rule->getCode();
        foreach (['robots\\.txt', 'sitemap\\.xml', 'ads\\.txt', 'humans\\.txt', 'llms\\.txt'] as $filename) {
            $this->assertStringContainsString($filename, $code, "Rule missing pattern for $filename");
        }
        $this->assertStringContainsString('\\.well-known/security\\.txt', $code);
    }

    // -------------------------------------------------------------------------
    // NginxGenerator — well_known_files rule
    // -------------------------------------------------------------------------

    public function testNginxRuleDeniesDirectAccessToSiteSpecificDirectory(): void
    {
        $rule = (new NginxGenerator())->getRule('well_known_files');
        $this->assertNotNull($rule);
        $code = $rule->getCode();
        $this->assertStringContainsString('location ^~ /application/files/site-specific/', $code);
        $this->assertStringContainsString('deny all', $code);
    }

    public function testNginxRuleValidatesHostBeforeUsingItAsFilesystemPath(): void
    {
        $rule = (new NginxGenerator())->getRule('well_known_files');
        $this->assertNotNull($rule);
        $code = $rule->getCode();
        // Host validation regex must gate the $ccm_site_dir variable assignment.
        $this->assertStringContainsString('if ($host ~*', $code);
        $this->assertStringContainsString('$ccm_site_dir /application/files/site-specific/$host', $code);
    }

    public function testNginxRuleRoutesAllowedFilenames(): void
    {
        $rule = (new NginxGenerator())->getRule('well_known_files');
        $this->assertNotNull($rule);
        $code = $rule->getCode();
        foreach (['robots.txt', 'sitemap.xml', 'ads.txt', 'humans.txt', 'llms.txt'] as $filename) {
            $this->assertStringContainsString($filename, $code, "Rule missing location for $filename");
        }
        $this->assertStringContainsString('.well-known/security.txt', $code);
    }
}
