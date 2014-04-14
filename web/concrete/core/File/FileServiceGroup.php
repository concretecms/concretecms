<?php 
namespace Concrete\Core\File;
use \Concrete\Core\Foundation\Service\Group as ServiceGroup;

class FileServiceGroup extends ServiceGroup {

	public function register() {
		$singletons = array(
			'file' => '\Concrete\Core\File\Service\File',
			'concrete/file' => '\Concrete\Core\File\Service\Application',
			'image' => '\Concrete\Core\File\Service\Image',
			'mime' => '\Concrete\Core\File\Service\Mime'
		);

		foreach($singletons as $key => $value) {
			$this->locator->singleton($key, $value);
		}
	}


}