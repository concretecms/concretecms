<?php
namespace Concrete\Core\Localization\Locale;

interface LocaleInterface
{

    function getLanguage();
    function getCountry();
    function getNumPlurals();
    function getPluralCases();
    function getPluralRule();
    function getLanguageText($locale = null);

}