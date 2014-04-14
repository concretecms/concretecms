<?php 
namespace Concrete\Core\Application;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider {

	public function register() {
		$singletons = array(
			'concrete/asset_library' => '\Concrete\Core\Application\Service\FileManager',
			'concrete/file_manager' => '\Concrete\Core\Application\Service\FileManager',
			'concrete/avatar' => '\Concrete\Core\Application\Service\Composer',
			'concrete/composer' => '\Concrete\Core\Application\Service\FileManager',
			'concrete/dashboard' => '\Concrete\Core\Application\Service\Dashboard',
			'concrete/dashboard/sitemap' => '\Concrete\Core\Application\Service\Dashboard\Sitemap',
			'concrete/image' => '\Concrete\Core\Application\Service\Image',
			'concrete/file_manager' => '\Concrete\Core\Application\Service\FileManager',
			'concrete/ui' => '\Concrete\Core\Application\Service\UI',
			'concrete/ui/menu' => '\Concrete\Core\Application\Service\UI\Menu',
			'concrete/ui/help' => '\Concrete\Core\Application\Service\UI\Help',
			'concrete/upgrade' => '\Concrete\Core\Application\Service\Upgrade',
			'concrete/urls' => '\Concrete\Core\Application\Service\Urls',
			'concrete/user' => '\Concrete\Core\Application\Service\User',
			'concrete/validation' => '\Concrete\Core\Application\Service\Validation',
			'rating' => '\Concrete\Attribute\Rating\Service'
		);

		foreach($singletons as $key => $value) {
			$this->app->singleton($key, $value);
		}
	}


}