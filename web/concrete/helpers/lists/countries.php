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
class ListsCountriesHelper {

	protected $countries = array();
	
	public function __construct() {
		Loader::library('3rdparty/Zend/Locale');
		$countries = Zend_Locale::getTranslationList('territory', Localization::activeLocale(), 2);
		// unset invalid countries
		unset($countries['SU'], $countries['ZZ'], $countries['IM'], $countries['JE'], $countries['VD']);
		asort($countries, SORT_LOCALE_STRING);
		$this->countries = $countries;
	}
	
	/** 
	 * Returns an array of Countries with their short name as the key and their full name as the value
	 * @return array
	 */
	public function getCountries() {
		return $this->countries;
	}
	

	/** 
	 * Gets a country full name given its index
	 * @return string
	 * @param string $index
	 */
	public function getCountryName($index) { return $this->countries[$index]; }

}