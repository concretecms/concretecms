<?php

class LocalizationTest extends \PHPUnit_Framework_TestCase
{
    public function testGeneratedPO()
    {
        // Create the .pot file with the strings taken from the source code
        $potFile = DIR_APPLICATION . '/' . DIRNAME_LANGUAGES . '/messages.pot';
        $cmd = array();
        $cmd[] = 'php ' . escapeshellarg(DIR_BUILDTOOLS . '/i18n.php');
        $cmd[] = '--webroot=' . escapeshellarg(DIR_BASE);
        $cmd[] = '--createpot=yes';
        $cmd[] = '--createpo=no';
        $cmd[] = '--compile=no';
        $cmd[] = '2>&1';
        $output = array();
        exec(implode(' ', $cmd), $output, $rc);
        $this->assertSame(0, $rc, "i18n.php output:\n" . implode("\n", $output));
        $this->assertFileExists($potFile);

        // Create a fake English .po file (translated string same as source strings)
        $poFile = DIR_APPLICATION . '/' . DIRNAME_LANGUAGES . '/messages.po';
        $cmd = array();
        $cmd[] = 'msgen';
        $cmd[] = '--lang=en-US';
        $cmd[] = '--output-file=' . escapeshellarg($poFile);
        $cmd[] = escapeshellarg($potFile);
        $cmd[] = '2>&1';
        $output = array();
        exec(implode(' ', $cmd), $output, $rc);
        $this->assertSame(0, $rc, "msgen output:\n" . implode("\n", $output));
        $this->assertFileExists($poFile);

        // Add missing header entries
        $poData = @file_get_contents($poFile);
        $this->assertSame('string', gettype($poData), 'Read .po file');
        $positionInHeader = strpos($poData, '"Language:');
        $this->assertSame('integer', gettype($positionInHeader), 'Find Language in po header');
        $poData =
            substr($poData, 0, $positionInHeader)
            . '"Plural-Forms: nplurals=2; plural=(n != 1)\n"' . "\n"
            . '"PO-Revision-Date: 2014-06-19 14:55+0000\n"' . "\n"
            . '"Last-Translator: \n"' . "\n"
            . '"Language-Team: \n"' . "\n"
            . substr($poData, $positionInHeader)
        ;
        $wroteBytes = @file_put_contents($poFile, $poData);
        $this->assertSame('integer', gettype($wroteBytes), 'Write patched .po file ');

        // Compile the fake .po file to a compiled .mo file
        $moFile = DIR_APPLICATION . '/' . DIRNAME_LANGUAGES . '/messages.mo';
        $cmd = array();
        $cmd[] = 'msgfmt';
        $cmd[] = '--check-format'; // check language dependent format strings
        $cmd[] = '--check-header'; // verify presence and contents of the header entry
        $cmd[] = '--check-domain'; // check for conflicts between domain directives and the --output-file option
        $cmd[] = '--verbose'; // increase verbosity level
        $cmd[] = '--output-file=' . escapeshellarg($moFile);
        $cmd[] = escapeshellarg($poFile);
        $cmd[] = '2>&1';
        $output = array();
        exec(implode(' ', $cmd), $output, $rc);
        $this->assertSame(0, $rc, "msgfmt output:\n" . implode("\n", $output));
        $this->assertFileExists($moFile);
    }
}
