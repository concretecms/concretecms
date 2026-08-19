<?php

namespace Concrete\Tests\Editor;

use Concrete\Core\Editor\LinkAbstractor;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;

/**
 * Regression test: LinkAbstractor::translateFrom()/translateFromEditMode()/export() used to call
 * uuid_is_valid($fID) directly, without first checking that $fID was actually a string. When a
 * <concrete-picture> tag had no fid attribute (or one that simple-html-dom returned as null), this
 * passed null straight into the UUID check's underlying preg_match() call, raising a PHP
 * deprecation/type error that was then captured and logged, blowing up unrelated tests (e.g.
 * ContentTest) with what looked like a DB logging failure.
 */
class LinkAbstractorUuidGuardTest extends ConcreteDatabaseTestCase
{
    protected $tables = [
        'SystemContentEditorSnippets',
    ];

    public function testTranslateFromDoesNotErrorOnPictureTagWithoutFid()
    {
        // A concrete-picture tag missing its fid attribute must not trigger a fatal/deprecation
        // error inside uuid_is_valid() when translateFrom() resolves it.
        $translated = LinkAbstractor::translateFrom('<p><concrete-picture /></p>');

        $this->assertIsString($translated);
    }

    public function testTranslateFromEditModeDoesNotErrorOnPictureTagWithoutFid()
    {
        $translated = LinkAbstractor::translateFromEditMode('<p><concrete-picture /></p>');

        $this->assertIsString($translated);
    }

    public function testExportDoesNotErrorOnPictureTagWithoutFid()
    {
        $translated = LinkAbstractor::export('<p><concrete-picture /></p>');

        $this->assertIsString($translated);
    }
}