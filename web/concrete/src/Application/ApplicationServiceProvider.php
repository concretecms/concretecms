<?php
namespace Concrete\Core\Application;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider {

	public function register() {
		$singletons = array(
			'helper/concrete/asset_library' => '\Concrete\Core\Application\Service\FileManager',
			'helper/concrete/file_manager' => '\Concrete\Core\Application\Service\FileManager',
			'helper/concrete/avatar' => '\Concrete\Core\Application\Service\Avatar',
			'helper/concrete/composer' => '\Concrete\Core\Application\Service\Composer',
			'helper/concrete/dashboard' => '\Concrete\Core\Application\Service\Dashboard',
			'helper/concrete/dashboard/sitemap' => '\Concrete\Core\Application\Service\Dashboard\Sitemap',
			'helper/concrete/ui' => '\Concrete\Core\Application\Service\UserInterface',
			'helper/concrete/ui/menu' => '\Concrete\Core\Application\Service\UserInterface\Menu',
			'helper/concrete/ui/help' => '\Concrete\Core\Application\Service\UserInterface\Help',
			'helper/concrete/upgrade' => '\Concrete\Core\Application\Service\Upgrade',
			'helper/concrete/urls' => '\Concrete\Core\Application\Service\Urls',
			'helper/concrete/user' => '\Concrete\Core\Application\Service\User',
			'helper/concrete/validation' => '\Concrete\Core\Application\Service\Validation',
			'helper/rating' => '\Concrete\Attribute\Rating\Service',
            'helper/pagination' => '\Concrete\Core\Legacy\Pagination',

			'help' => '\Concrete\Core\Application\Service\UserInterface\Help',
			'help/core' => '\Concrete\Core\Application\Service\UserInterface\Help\CoreManager',
			'help/dashboard' => '\Concrete\Core\Application\Service\UserInterface\Help\DashboardManager',
			'help/block_type' => '\Concrete\Core\Application\Service\UserInterface\Help\BlockTypeManager',
			'help/panel' => '\Concrete\Core\Application\Service\UserInterface\Help\PanelManager'
		);

		foreach($singletons as $key => $value) {
			$this->app->singleton($key, $value);
		}

        $this->app->bind('error', 'Concrete\Core\Error\Error');
	}

}
