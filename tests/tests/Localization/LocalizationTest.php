<?php

use Concrete\Tests\Localization\LocalizationTestsBase;

class LocalizationTest extends LocalizationTestsBase
{
    public function testGeneratedPO()
    {
        $translationsFolder = static::getTranslationsFolder();

        // Extract the translatable strings and create the template .pot file
        $potFile = $translationsFolder.'/messages.pot';
        $translations = new \Gettext\Translations();
        $cifParser = new \C5TL\Parser\Cif();
        $phpParser = new \C5TL\Parser\Php();
        $blockTemplatesParser = new \C5TL\Parser\BlockTemplates();
        $themesPresetsParser = new \C5TL\Parser\ThemePresets();
        $parsersForDir = array(
            '' => array($phpParser),
            DIRNAME_BLOCKS => array($blockTemplatesParser),
            DIRNAME_THEMES => array($phpParser, $themesPresetsParser, $blockTemplatesParser),
            'config/install' => array($cifParser),
        );
        foreach ($parsersForDir as $parsersDir => $parsers) {
            $dir = DIR_BASE_CORE;
            $relDir = DIRNAME_CORE;
            if ($parsersDir !== '') {
                $dir .= '/'.$parsersDir;
                $relDir  .= '/'.$parsersDir;
            }
            foreach ($parsers as $parser) {
                try {
                    $parser->parseDirectory($dir, $relDir, $translations);
                } catch (Exception $x) {
                    $reason = $x->getMessage();
                    $stack = $x->getTraceAsString();
                    $this->markTestSkipped(<<<EOT
Extraction of translatable strings has been skipped.
Reason: $reason
Stack trace:
$stack
EOT
                    );

                    return;
                }
            }
        }
        $translatableStrings = count($translations);
        $this->assertGreaterThan(2000, $translatableStrings);
        $translations->toPoFile($potFile);
        $this->assertFileExists($potFile);

        // Create a .po file with translations set to source strings
        $poFile = $translationsFolder.'/messages.po';
        $cmd = 'msgen --lang=en-US --output-file='.escapeshellarg($poFile).' '.escapeshellarg($potFile).' 2>&1';
        $output = array();
        @exec($cmd, $output, $rc);
        $this->assertSame(0, $rc, "msgen output:\n" . implode("\n", $output));
        $this->assertFileExists($poFile);

        // Set the plural rules
        $translations = \Gettext\Translations::fromPoFile($poFile);
        $translations->setLanguage('en_US');
        $translations->toPoFile($poFile);
        $this->assertSame($translatableStrings, count($translations));

        // Compile the .po file checking the strings
        $moFile = $translationsFolder.'/messages.mo';
        $cmd = 'msgfmt';
        $cmd .= ' --check-format'; // check language dependent format strings
        $cmd .= ' --check-header'; // verify presence and contents of the header entry
        $cmd .= ' --check-domain'; // check for conflicts between domain directives and the --output-file option
        $cmd .= ' --verbose'; // increase verbosity level
        $cmd .= ' --output-file='.escapeshellarg($moFile);
        $cmd .= ' '.escapeshellarg($poFile);
        $cmd .= ' 2>&1';
        $output = array();
        @exec($cmd, $output, $rc);
        $this->assertSame(0, $rc, "msgfmt output:\n" . implode("\n", $output));
        $this->assertFileExists($moFile);
    }
}
