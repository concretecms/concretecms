<?php
namespace Concrete\Core\Localization\Locale;

interface LocaleInterface
{

    function getLocaleID();
    function getLanguage();
    function getCountry();
    function getLocale();
    function getNumPlurals();
    function getPluralCases();
    function getPluralRule();
    function setNumPlurals($numPlurals);
    function setPluralCases($numCases);
    function setPluralRule($pluralRule);
    function getLanguageText($locale = null);

}