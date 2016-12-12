<?php 
namespace Concrete\Core\Localization;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class LocalizationServiceProvider extends ServiceProvider {

	public function register() {
		$singletons = array(
			'helper/localization/countries' => '\Concrete\Core\Localization\Service\CountryList',
			'helper/localization/states_provinces' => '\Concrete\Core\Localization\Service\StatesProvincesList',
			'helper/lists/countries' => '\Concrete\Core\Localization\Service\CountryList',
			'helper/lists/states_provinces' => '\Concrete\Core\Localization\Service\StatesProvincesList',
			'helper/date' => '\Concrete\Core\Localization\Service\Date',

			'localization/countries' => '\Concrete\Core\Localization\Service\CountryList',
			'localization/states_provinces' => '\Concrete\Core\Localization\Service\StatesProvincesList',
            'localization/languages' => '\Concrete\Core\Localization\Service\LanguageList',
			'lists/countries' => '\Concrete\Core\Localization\Service\CountryList',
			'lists/states_provinces' => '\Concrete\Core\Localization\Service\StatesProvincesList',
			'date' => '\Concrete\Core\Localization\Service\Date'
		);

		foreach($singletons as $key => $value) {
			$this->app->singleton($key, $value);
		}
	}


}