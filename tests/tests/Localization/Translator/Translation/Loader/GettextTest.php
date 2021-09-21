<?php

namespace Concrete\Tests\Localization\Translator\Translation\Loader;

use Concrete\Core\Localization\Translator\Loader\Gettext;
use Gettext\Languages\Language;
use PHPUnit_Framework_TestCase;

class GettextTest extends PHPUnit_Framework_TestCase
{
    public function provideActualNumPlurals()
    {
        return [
            [1],
            [2],
            [3],
            [4],
        ];
    }

    /**
     * @dataProvider provideActualNumPlurals
     */
    public function testFixingPlurals($actualNumPlurals)
    {
        $localeInfo = Language::getById('fr_FR');
        $expectedNumPlurals = count($localeInfo->categories);
        $loader = new Gettext(DIR_BASE);
        $textDomain = $loader->load('fr_FR', DIR_TESTS . "/assets/Localization/Translator/Loader/Gettext/fr_FR-{$actualNumPlurals}-plurals.mo");
        for ($which = 1; $which <= 2; $which++) {
            $this->assertSame("[{$which}] Singular: translated", $textDomain["[{$which}] Singular"]);
            if ($expectedNumPlurals === 1 || $actualNumPlurals === 1) {
                $this->assertSame("[{$which}] Plural - Other: translated", $textDomain["[{$which}] Plural - One"]);
            } else {
                $this->assertInternalType('array', $textDomain["[{$which}] Plural - One"]);
                $this->assertGreaterThanOrEqual(count($localeInfo->categories), count($textDomain["[{$which}] Plural - One"]));
            }
        }
    }
}
