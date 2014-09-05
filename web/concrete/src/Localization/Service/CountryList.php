<?php
namespace Concrete\Core\Localization\Service;

use Localization;
use Events;

class CountryList {

	protected $countries = array();

	public function __construct() {

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
