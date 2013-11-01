<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Grabs a list of countries commonly used in web forms.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Lists_Countries {

	protected $countries = array();

	public function __construct() {
		Loader::library('3rdparty/Zend/Locale');
		$countries = Zend_Locale::getTranslationList('territory', Localization::activeLocale(), 2);
		unset(
			// Fake countries
			$countries['FX'], // Metropolitan France (it's not a country, but its the part of France located in Europe, but we've already FR - France)
			$countries['IM'], // Isle of Man (it's a British Crown Dependency)
			$countries['JE'], // Jersey (it's a British Crown Dependency)
			$countries['NT'], // Neutral Zone
			$countries['PU'], // U.S. Miscellaneous Pacific Islands
			$countries['ZZ'], // Unknown or Invalid Region
			// Dismissed countries
			$countries['CS'], // Serbia and Montenegro (since 2006 has been spitted in Serbia and Montenegro)
			$countries['CT'], // Canton and Enderbury Islands (merged into Kiribati since 1979)
			$countries['DD'], // East Germany (merged with West Germany into Germany in 1990)
			$countries['PC'], // Pacific Islands Trust Territory (no more existing since 1994)
			$countries['PZ'], // Panama Canal Zone (merged into Panama since 2000)
			$countries['SU'], // Union of Soviet Socialist Republics (splitted into several countries since 1991)
			$countries['VD'], // North Vietnam (merged with South Vietnam into Socialist Republic of Vietnam in 1976)
			$countries['YD']  // People's Democratic Republic of Yemen (no more existing since 1990)
		);
		$countriesFromEvent = Events::fire('on_get_countries_list', $countries);
		if(is_array($countriesFromEvent)) {
			$countries = $countriesFromEvent;
		} else {
			asort($countries, SORT_LOCALE_STRING);
		}
		$this->countries = $countries;
	}

	/** Returns an array of countries with their short name as the key and their full name as the value
	* @return array Keys are the country codes, values are the county names
	*/
	public function getCountries() {
		return $this->countries;
	}

	/** Gets a country full name given its code
	* @param string $code The country code
	* @return string
	*/
	public function getCountryName($code) {
		$countries = $this->getCountries(true);
		return $countries[$code];
	}

}
