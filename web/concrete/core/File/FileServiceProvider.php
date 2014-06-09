<?php 
namespace Concrete\Core\File;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class FileServiceProvider extends ServiceProvider {

	public function register() {
		$singletons = array(
			'helper/file' => '\Concrete\Core\File\Service\File',
			'helper/concrete/file' => '\Concrete\Core\File\Service\Application',
			'helper/image' => '\Concrete\Core\Legacy\ImageHelper', /* deprecated */
			'helper/mime' => '\Concrete\Core\File\Service\Mime'
		);

		foreach($singletons as $key => $value) {
			$this->app->singleton($key, $value);
		}

        $this->app->bind('image/imagick', '\Imagine\Imagick\Imagine');
        $this->app->bind('image/gd', '\Imagine\Gd\Imagine');
	}


}