<?php 
namespace Concrete\Core\Multilingual;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class MultilingualServiceProvider extends ServiceProvider {

	public function register() {
		$singletons = array(
			'multilingual/interface/flag' => '\Concrete\Core\Multilingual\Service\UserInterface\Flag',
			'multilingual/detector' => '\Concrete\Core\Multilingual\Service\Detector',
			'multilingual/extractor' => '\Concrete\Core\Multilingual\Service\Extractor'
		);

		foreach($singletons as $key => $value) {
			$this->app->singleton($key, $value);
		}

	}


}