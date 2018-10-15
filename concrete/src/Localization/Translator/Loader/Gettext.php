<?php

namespace Concrete\Core\Localization\Translator\Loader;

use Gettext\Languages\Language;
use Zend\I18n\Exception\RuntimeException;
use Zend\I18n\Translator\Loader\Gettext as ZendGettext;
use Zend\I18n\Translator\Plural\Rule;
use Zend\I18n\Translator\TextDomain;

class Gettext extends ZendGettext
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
     * @see \Zend\I18n\Translator\Loader\Gettext::load()
     */
    public function load($locale, $filename)
    {
        $textDomain = parent::load($locale, $filename);

        $localeInfo = Language::getById($locale);
        if ($localeInfo !== null) {
            $this->fixPlurals($filename, $textDomain, $localeInfo);
        }

        return $textDomain;
    }

    /**
     * Fix the plural rules of the translations loaded from a file.
     *
     * @param string $filename
     * @param \Zend\I18n\Translator\TextDomain $textDomain
     * @param \Gettext\Languages\Language $localeInfo
     *
     * @throws \Zend\I18n\Exception\RuntimeException when the loaded file has less plural rules than the required ones
     */
    private function fixPlurals($filename, TextDomain $textDomain, Language $localeInfo)
    {
        $expectedNumPlurals = count($localeInfo->categories);
        $pluralRule = $textDomain->getPluralRule(false);
        if ($pluralRule === null) {
            // Build the plural rules
            $pluralRule = Rule::fromString("nplurals={$expectedNumPlurals}; plural={$localeInfo->formula};");
            $textDomain->setPluralRule($pluralRule);
        } else {
            $readNumPlurals = $pluralRule->getNumPlurals();
            if ($expectedNumPlurals < $readNumPlurals) {
                // Reduce the number of plural rules, in order to consider other systems that use wrong counts (for example, Transifex uses 4 plural rules, but gettext only defines 3)
                $pluralRule = Rule::fromString("nplurals={$expectedNumPlurals}; plural={$localeInfo->formula};");
                $textDomain->setPluralRule($pluralRule);
            } elseif ($expectedNumPlurals > $readNumPlurals) {
                // The language file defines less plurals than the required ones.
                $filename = str_replace(DIRECTORY_SEPARATOR, '/', $filename);
                if (strpos($filename, $this->webrootDirectory . '/') === 0) {
                    $filename = substr($filename, strlen($this->webrootDirectory));
                }
                // Don't translate the message, otherwise we may have an infinite loop (t() -> load translations -> t() -> load translations -> ...)
                throw new RuntimeException(sprintf(
                    $readNumPlurals === 1 ?
                        'The language file %1$s for %2$s has %3$s plural form instead of %4$s.'
                        :
                        'The language file %1$s for %2$s has %3$s plural forms instead of %4$s.'
                    ,
                    $filename,
                    $localeInfo->name,
                    $readNumPlurals,
                    $expectedNumPlurals
                ));
            }
        }

        return $textDomain;
    }
}
