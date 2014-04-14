<?php 
namespace Concrete\Core\Localization;
use \Concrete\Core\Foundation\Service\Group as ServiceGroup;

class LocalizationServiceGroup extends ServiceGroup {

	public function register() {
		$singletons = array(
			'localization/countries' => '\Concrete\Core\Localization\Service\CountryList',
			'localization/states_provinces' => '\Concrete\Core\Localization\Service\StatesProvincesList',
			'lists/countries' => '\Concrete\Core\Localization\Service\CountryList',
			'lists/states_provinces' => '\Concrete\Core\Localization\Service\StatesProvincesList',
			'date' => '\Concrete\Core\Localization\Service\Date'

		);

		foreach($singletons as $key => $value) {
			$this->locator->singleton($key, $value);
		}
	}


}