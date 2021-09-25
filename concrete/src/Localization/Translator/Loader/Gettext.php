<?php

namespace Concrete\Core\Localization\Translator\Loader;

use Gettext\Languages\Language;
use Laminas\I18n\Translator\Loader\Gettext as LaminasGettext;
use Laminas\I18n\Translator\Plural\Rule;
use Laminas\I18n\Translator\TextDomain;

class Gettext extends LaminasGettext
{
    /**
     * Tthe absolute path of the web root.
     *
     * @var string
     */
    private $webrootDirectory;

    /**
     * Initialize the instance.
     *
     * @param string $webrootDirectory the absolute path of the web root
     */
    public function __construct($webrootDirectory)
    {
        $this->webrootDirectory = $webrootDirectory;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Laminas\I18n\Translator\Loader\Gettext::load()
     */
    public function load($locale, $filename)
    {
        $textDomain = parent::load($locale, $filename);

        $localeInfo = Language::getById($locale);
        if ($localeInfo !== null) {
            $this->fixPlurals($textDomain, $localeInfo);
        }

        return $textDomain;
    }

    /**
     * Fix the plural rules of the translations loaded from a file.
     *
     * @param \Laminas\I18n\Translator\TextDomain $textDomain
     * @param \Gettext\Languages\Language $localeInfo
     *
     * @return \Laminas\I18n\Translator\TextDomain
     */
    private function fixPlurals(TextDomain $textDomain, Language $localeInfo)
    {
        $expectedNumPlurals = count($localeInfo->categories);
        $actualPluralRule = $textDomain->getPluralRule(false);
        if ($actualPluralRule === null) {
            // Build the plural rules
            $pluralRule = Rule::fromString("nplurals={$expectedNumPlurals}; plural={$localeInfo->formula};");
            $textDomain->setPluralRule($pluralRule);
        } else {
            $actualNumPlurals = $actualPluralRule->getNumPlurals();
            if ($expectedNumPlurals !== $actualNumPlurals) {
                // Adjust the number of plural rules, in order to consider other systems that different plural rule counts
                $pluralRule = Rule::fromString("nplurals={$expectedNumPlurals}; plural={$localeInfo->formula};");
                $textDomain->setPluralRule($pluralRule);
                if ($actualNumPlurals < $expectedNumPlurals) {
                    $maxPluralIndex = $expectedNumPlurals - 1;
                    foreach (array_filter($textDomain->getArrayCopy(), 'is_array') as $messageID => $translations) {
                        $lastValue = end($translations);
                        if ($expectedNumPlurals === 1) {
                            $translations = $lastValue;
                        } else {
                            while (!isset($translations[$maxPluralIndex])) {
                                $translations[] = $lastValue;
                            }
                        }
                        $textDomain[$messageID] = $translations;
                    }
                }
            }
        }

        return $textDomain;
    }
}
