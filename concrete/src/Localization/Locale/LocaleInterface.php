<?php
namespace Concrete\Core\Localization\Locale;

interface LocaleInterface
{
    public function getLocaleID();

    public function getLanguage();

    public function getCountry();

    public function getLocale();

    public function getNumPlurals();

    public function getPluralCases();

    public function getPluralRule();

    public function setNumPlurals($numPlurals);

    public function setPluralCases($numCases);

    public function setPluralRule($pluralRule);

    public function getLanguageText($locale = null);
}
