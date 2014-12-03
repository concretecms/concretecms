<?php

namespace Concrete\Core\Localization\Service;
use Punic\Data;
use Punic\Language;
use Punic\Territory;
use Localization;

defined('C5_EXECUTE') or die("Access Denied.");

class LanguageList
{
    /**
     * Returns an associative array with the locale code as the key and the translated language name as the value.
     * @return array
     */
    public function getLanguageList()
    {
        $locales = Data::getAvailableLocales();
        $languages = array();
        foreach($locales as $locale) {
            $split = explode('-', $locale);
            $language = $split[0];
            $languages[$language] = Language::getName($language, Localization::activeLocale());
        }
        asort($languages);
        return $languages;
    }

    /**
     * Returns a list of countries that speak the passed language
     *
     */
    public function getLanguageCountries($language)
    {
        // first, we retrieve all the locales
        $locales = Data::getAvailableLocales();

        // now, we get just the locales that use this language
        $languageLocales = array_filter($locales, function($item) use ($language) {
            $split = explode('-', $item);
            return strtolower($split[0]) == strtolower($language);
        });

        // now we loop through all countries and if their locale is in $languageLocales we return them
        $countries = array();
        foreach($languageLocales as $locale) {
            $territory = strtolower(Data::getTerritory($locale));
            $countries[$territory] = Territory::getName($territory);
        }

        return $countries;

    }

}
