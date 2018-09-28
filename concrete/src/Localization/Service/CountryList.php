<?php
namespace Concrete\Core\Localization\Service;

use Events;
use Localization;

class CountryList
{
    protected $countries = [];

    public function __construct()
    {
        $this->loadCountries();
    }

    protected function loadCountries()
    {
        $countries = \Punic\Territory::getCountries();
        unset(
            // Fake countries
            $countries['IM'], // Isle of Man (it's a British Crown Dependency)
            $countries['JE'] // Jersey (it's a British Crown Dependency)
        );

        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('countries', $countries);
        $event = Events::dispatch('on_get_countries_list', $event);
        $countries = $event->getArgument('countries');

        $this->countries[Localization::activeLocale()] = $countries;
    }

    /** Returns an array of countries with their short name as the key and their full name as the value
     * @return array Keys are the country codes, values are the county names
     */
    public function getCountries()
    {
        if (!array_key_exists(Localization::activeLocale(), $this->countries)) {
            $this->loadCountries();
        }

        return $this->countries[Localization::activeLocale()];
    }

    /** Gets a country full name given its code
     * @param string $code The country code
     *
     * @return string
     */
    public function getCountryName($code)
    {
        $countries = $this->getCountries(true);

        return $countries[$code];
    }

    /**
     * Return a list of territory codes where a specific language is spoken, sorted by the total number of people speaking that language.
     *
     * @param string $languageCode The language code (eg. 'en')
     * @param string $languageStatuses The allowed statuses of the languages, whose codes are 'o' (official), 'r' (official regional), 'f' (de facto official), 'm' (official minority), 'u' (unofficial or unknown)
     *
     * @return array Returns a list of country codes
     */
    public function getCountriesForLanguage($languageCode, $languageStatuses = 'orfm')
    {
        $territories = [];
        foreach (\Punic\Territory::getTerritoriesForLanguage($languageCode) as $territory) {
            $territoryLanguages = \Punic\Territory::getLanguages($territory, $languageStatuses, true);
            if (in_array($languageCode, $territoryLanguages)) {
                $territories[] = $territory;
            }
        }
        
        $validCountryCodes = array_keys($this->getCountries());
        $result = array_intersect($territories, $validCountryCodes);

        return array_values($result);
    }
}
